<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace App\VacReqBundle\Controller;

use App\UserdirectoryBundle\Controller\OrderAbstractController;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//vacreq site

class DefaultController extends OrderAbstractController
{

    /**
     * @Route("/about", name="vacreq_about_page")
     * @Template("AppUserdirectoryBundle/Default/about.html.twig")
     */
    public function aboutAction( Request $request ) {
        return array('sitename'=>$this->getParameter('vacreq.sitename'));
    }

//    /**
//     * @Route("/", name="vacreq_home")
//     * @Template("AppVacReqBundle/Request/index.html.twig", methods={"GET"})
//     */
//    public function indexAction()
//    {
//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_USER') ) {
//            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
//        }
//
//        $em = $this->getDoctrine()->getManager();
//
//        $vacReqRequests = $em->getRepository('AppVacReqBundle:VacReqRequest')->findAll();
//
//        return array(
//            'vacReqRequests' => $vacReqRequests
//        );
//    }


    /**
     * //@Route("/download-spreadsheet-with-ids/{ids}", name="vacreq_download_spreadsheet_get_ids")
     *
     * @Route("/download-spreadsheet/", name="vacreq_download_spreadsheet", methods={"POST"})
     */
    public function downloadExcelAction( Request $request ) {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_USER') ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $vacreqUtil = $this->get('vacreq_util');


        $ids = $request->request->get('ids');
        //echo "ids=".$ids."<br>";
        //exit('111');

        $fileName = "Stats".".xlsx";

        if(0) {
            $fileName = "PhpOffice_".$fileName;

            $excelBlob = $vacreqUtil->createtListExcel($ids);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excelBlob, 'Xlsx');
            //ob_end_clean();
            //$writer->setIncludeCharts(true);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            //header('Content-Disposition: attachment;filename="fileres.xlsx"');

            // Write file to the browser
            $writer->save('php://output');
        } else {
            //Spout
            $vacreqUtil->createtListExcelSpout( $ids, $fileName );
        }

        exit();
    }

    /**
     * http://127.0.0.1/order/index_dev.php/vacation-request/multiple-carry-over-requests
     *
     * @Route("/multiple-carry-over-requests", name="vacreq_multiple_carry_over_requests")
     */
    public function multipleCarryOverRequestsAction( Request $request ) {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_USER') ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        //exit('Not allowed.');

        $em = $this->getDoctrine()->getManager();

        $status = 'approved';

        //1) get carry-over VacReqRequest with the same year and user
        $repository = $em->getRepository('AppVacReqBundle:VacReqRequest');
        $dql =  $repository->createQueryBuilder("request");

        $dql->select('request');
        //$dql->select('DISTINCT user.id, requestType.startDate, requestType.endDate, requestType.numberOfDays as numberOfDays');
        //$dql->select('DISTINCT user.id');

        $dql->leftJoin("request.user", "user");
        $dql->leftJoin("request.requestType", "requestType");

        $dql->where("requestType.abbreviation = 'carryover'");

        $dql->andWhere("request.status = :status");
        $params['status'] = $status;

        //$dql->andWhere("request.destinationYear = :destinationYear");
        //$params['destinationYear'] = $year;

        $query = $em->createQuery($dql);

        if( count($params) > 0 ) {
            $query->setParameters($params);
        }

        $requests = $query->getResult();
        echo "requests=".count($requests)."<br>";

        $carryOverRequests = array();
        //$carryOverDays = array();

        if(1) {
            foreach ($requests as $thisRequest) {
                $user = $thisRequest->getUser();
                $user = $user . "";
                $destinationYear = $thisRequest->getDestinationYear();

                if (isset($carryOverRequests[$user][$destinationYear])) {
                    $count = $carryOverRequests[$user][$destinationYear];
                    $count++;
                    $carryOverRequests[$user][$destinationYear] = $count;
                } else {
                    $carryOverRequests[$user][$destinationYear] = 1;
                }
            }
            echo "carryOverRequests=" . count($carryOverRequests) . "<br><br>";


            foreach ($carryOverRequests as $userId => $userCarryOverRequest) {
                //echo "userId=".$userId."<br>";
                foreach ($userCarryOverRequest as $destinationYear => $userCarryOverRequest[$userId]) {
                    //echo $thisCarryOverRequest[$userId][$destinationYear]."<br>";
                    //echo "destinationYear=$destinationYear <br>";
                    //echo "count=".$userCarryOverRequest[$userId][$destinationYear]."<br>";
                    $count = $carryOverRequests[$userId][$destinationYear];
                    //echo "$userId: $destinationYear => $count ";
                    if ($count > 1) {
                        echo "$userId: $destinationYear => $count ";
                        echo "=> Duplicate !!!";
                        echo "<br>";
                    }
                    //echo "<br>";
                }

            }
        }

//        foreach ($requests as $thisRequest) {
//            $user = $thisRequest->getUser();
//            $user = $user . "";
//            $destinationYear = $thisRequest->getDestinationYear()."";
//            $thisDay = $thisRequest->getCarryOverDays();
//
//            if (isset($carryOverDays[$user][$destinationYear])) {
//                $days = $carryOverDays[$user][$destinationYear];
//                $days = $days + $thisDay;
//                $carryOverDays[$user][$destinationYear] = $days;
//            } else {
//                $carryOverDays[$user][$destinationYear] = $thisDay;
//            }
//
//        }
//
//        foreach($carryOverDays as $userId=>$userCarryOverDays ) {
//            //echo "userId=".$userId."<br>";
//            foreach($userCarryOverDays as $destinationYear=>$userCarryOverDays[$userId]) {
//                $days = $carryOverDays[$user][$destinationYear];
//                echo "$userId: $destinationYear => $days ";
//                echo "<br>";
//            }
//
//        }

        exit('EOF multipleCarryOverRequestsAction');
    }

    /**
     * http://127.0.0.1/order/index_dev.php/vacation-request/diff-carry-over-days
     *
     * @Route("/diff-carry-over-days", name="vacreq_diff_carry_over_days")
     */
    public function diffCarryOverDaysAction( Request $request )
    {
        if (false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_USER')) {
            return $this->redirect($this->generateUrl('vacreq-nopermission'));
        }

        //exit('Not allowed.');

        $vacreqUtil = $this->get('vacreq_util');
        $em = $this->getDoctrine()->getManager();

        $status = 'approved';

        //1) for all VacReqCarryOver => get days
        //2) find approved carry over request for this user and year
        //3) compare days

        $repository = $em->getRepository('AppVacReqBundle:VacReqCarryOver');
        $dql =  $repository->createQueryBuilder("carryover");

        $dql->select('carryover');

        $dql->leftJoin("carryover.userCarryOver", "userCarryOver");

        //$dql->leftJoin("userCarryOver.requestType", "requestType");
        //$dql->leftJoin("userCarryOver.user", "user");
        //$dql->where("requestType.abbreviation = 'carryover'");

        //$dql->andWhere("request.status = :status");
        //$params['status'] = $status;

        //$dql->andWhere("request.destinationYear = :destinationYear");
        //$params['destinationYear'] = $year;

        $query = $em->createQuery($dql);

//        if( count($params) > 0 ) {
//            $query->setParameters($params);
//        }

        $carryovers = $query->getResult();
        echo "carryovers=".count($carryovers)."<br>";

        foreach($carryovers as $carryover) {
            $carryOverUser = $carryover->getUserCarryOver();
            $user = $carryOverUser->getUser();
            $days = $carryover->getDays();
            $year = $carryover->getYear();

            $approvedRequests = $vacreqUtil->getCarryOverRequestsByUserStatusYear($user,'approved',$year);
            //echo "approvedRequests=".count($approvedRequests)."<br>";

            if( count($approvedRequests) > 1 ) {
                echo "$user: $year => $days days";
                echo "=> Duplicate !!!";
                echo "<br>";
            }

            if( count($approvedRequests) == 1 ) {
                $approvedRequest = $approvedRequests[0];
                $thisDays = $approvedRequest->getCarryOverDays();

                if( $thisDays != $days ) {
                    echo "$user: $year => Diff!!!: [$days] != [$thisDays]";
                    echo "<br>";
                }
            }
        }

        exit('EOF diffCarryOverDaysAction');
    }

}
