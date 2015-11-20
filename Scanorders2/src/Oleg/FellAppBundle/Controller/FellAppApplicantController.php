<?php
/**
 * Created by PhpStorm.
 * User: oli2002
 * Date: 11/11/15
 * Time: 3:42 PM
 */

namespace Oleg\FellAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use PhpOffice\PhpWord\PhpWord;

use Oleg\FellAppBundle\Entity\FellowshipApplication;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Util\EmailUtil;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class FellAppApplicantController extends Controller {



    /**
     * @Route("/interview-modal/{id}", name="fellapp_interview_modal")
     * @Method("GET")
     * @Template("OlegFellAppBundle:Interview:modal.html.twig")
     */
    public function interviewModalAction(Request $request, $id) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_USER') ) {
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //echo "invite interviewers to rate <br>";
        //exit();
        $res = "";

        //$logger = $this->container->get('logger');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }



        return array(
            'entity' => $entity,
            'pathbase' => 'fellapp',
            'sitename' => $this->container->getParameter('fellapp.sitename')
        );
    }




    /**
     * @Route("/interview-score-rank/{id}", name="fellapp_interviewe_score_rank")
     * @Method("GET")
     */
    public function intervieweScoreRankAction(Request $request, $id) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_USER') ) {
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //echo "invite interviewers to rate <br>";
        //exit();
        $res = "";

        //$logger = $this->container->get('logger');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }

        if( $entity->getInterviewScore() == null || $entity->getInterviewScore() <= 0 ) {
            $response = new Response();
            $response->setContent($res);
            return $response;
        }

        $fellappType = $entity->getFellowshipSubspecialty();

        $startDate = $entity->getStartDate();
        $transformer = new DateTimeToStringTransformer(null,null,'d/m/Y');
        //$startDateStr = $transformer->transform($startDate);

//        $applicants = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);
        $repository = $em->getRepository('OlegFellAppBundle:FellowshipApplication');
        $dql = $repository->createQueryBuilder("fellapp");
        //TODO: optimize this by a single query without foreach loop
//        ->select('((SELECT COUNT(1) AS num FROM stats  WHERE stats.marks  > s.marks ) + 1)  AS rank')
//        ->from('Stats s')
//        ->where('s.user_id = ?', $user_id )
//        ->orderBy('rank');
        //$dql->select('((SELECT COUNT(1) AS num FROM stats  WHERE stats.marks  > s.marks ) + 1)  AS rank');
        $dql->select('fellapp');
        $dql->leftJoin("fellapp.fellowshipSubspecialty", "fellowshipSubspecialty");

        $dql->where("fellowshipSubspecialty.id = " . $fellappType->getId() );

        $startDateStr = $startDate->format('Y');
        $bottomDate = "01-01-".$startDateStr;
        $topDate = "12-31-".$startDateStr;
        $dql->andWhere("fellapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );

        $dql->andWhere("fellapp.interviewScore IS NOT NULL AND fellapp.interviewScore != '0'");

        $dql->orderBy("fellapp.interviewScore","ASC");

        $query = $em->createQuery($dql);
        $applicantions = $query->getResult();

        //echo "applicants=".count($applicantions)."<br>";

        if( count($applicantions) > 0 ) {

            $rank = 1;
            foreach( $applicantions as $applicantion ) {
                if( $applicantion->getId() == $id ) {
                    break;
                }
                $rank++;
            }

            //Combined Interview Score: X (Nth best of M available in [Fellowship specialty] for [Year])
            //Combined Interview Score: 3.3 (1st best of 6 available in Cytopathology for 2017)

            $rankStr = $rank."th";

            if( $rank == 1 ) {
                $rankStr = $rank."st";
            }
            if( $rank == 2 ) {
                $rankStr = $rank."nd";
            }
            if( $rank == 3 ) {
                $rankStr = $rank."rd";
            }

            $res = "Interview Score: ".
                $entity->getInterviewScore().
                " (".$rankStr." best of ".count($applicantions).
                " available in ".$fellappType." for ".$startDateStr.")";

        }

        $response = new Response();
        $response->setContent($res);
        return $response;
    }



    /**
     * @Route("/invite-interviewers-to-rate/{id}", name="fellapp_inviteinterviewerstorate")
     * @Method("GET")
     */
    public function inviteInterviewersToRateAction(Request $request, $id) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') && false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //echo "invite interviewers to rate <br>";
        //exit();
        //$logger = $this->container->get('logger');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }

        $emailUtil = new EmailUtil();
        $emails = array();

        //get all interviews
        foreach( $entity->getInterviews() as $interview ) {
            if( !$interview->getTotalRank() || $interview->getTotalRank() <= 0 ) {
                //send email to interviewer with links to PDF and Interview object to fill out.
                $email = $this->sendInvitationEmail($interview,$emailUtil);
                if( $email ) {
                    $emails[] = $email;
                }
            }
        }

        $event = "Invited interviewers to rate fellowship application ID " . $id . ".";
        $this->sendConfirmationEmail($emails,$entity,$event,$emailUtil,$request);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode("ok"));
        return $response;
    }

    /**
     * @Route("/invite-interviewer-to-rate/{interviewId}", name="fellapp_invite_single_interviewer_to_rate")
     * @Method("GET")
     */
    public function inviteSingleInterviewerToRateAction(Request $request, $interviewId) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') && false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //echo "invite interviewers to rate <br>";
        //exit();
        //$logger = $this->container->get('logger');

        $em = $this->getDoctrine()->getManager();

        $interview = $em->getRepository('OlegFellAppBundle:Interview')->find($interviewId);

        if( !$interviewId ) {
            throw $this->createNotFoundException('Interviewer can not be found: interviewId='.$interviewId);
        }

        $emailUtil = new EmailUtil();

        $email = $this->sendInvitationEmail($interview,$emailUtil);

        $fellapp = $interview->getFellapp();
        $emails = array();
        $emails[] = $email;

        $event = "Invited interviewer to rate fellowship application ID " . $fellapp->getId() . ".";
        $this->sendConfirmationEmail($emails,$fellapp,$event,$emailUtil,$request);


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode("ok"));
        return $response;
    }


    public function sendInvitationEmail( $interview, $emailUtil=null) {

        $logger = $this->container->get('logger');
        $em = $this->getDoctrine()->getManager();
        $fellapp = $interview->getFellapp();
        $applicant = $fellapp->getUser();
        $interviewer = $interview->getInterviewer();

        if( !$interviewer ) {
            $logger->error("sendInvitationEmail: No interviewer exists for interview=" . $interview );
            return null;
        }

        if( !$fellapp->getRecentItinerary() ) {
            $appLink = $this->generateUrl( 'fellapp_show', array("id"=>$fellapp->getId()), true );
            $appHref = '<a href="'.$appLink.'">'.$applicant->getUsernameOptimal().' (ID: '.$fellapp->getId().')'.'</a>';
            $this->get('session')->getFlashBag()->add(
                'warning',
                'Email invitations to evaluate '.$appHref.' have not been sent. Please upload Itinerary and try again.'
            );

            $logger->error("sendInvitationEmail: No recent itinerary found for fellapp ID=" . $fellapp->getId() );
            return null;
        }

        //get email
        $email = $interviewer->getEmail();

        //$userutil = new UserUtil();
        //$adminemail = $userutil->getSiteSetting($em,'siteEmail');
        $user = $this->get('security.context')->getToken()->getUser();
        $senderEmail = $user->getEmail();

        //fellapp_file_download
        $scheduleDocumentId = $fellapp->getRecentItinerary()->getId();
        $scheduleLink = $this->generateUrl( 'fellapp_file_download', array("id"=>$scheduleDocumentId), true );

        //fellapp_interview_edit
        $interviewFormLink = $this->generateUrl( 'fellapp_interview_edit', array("id"=>$interview->getId()), true );

        $pdfLink = $this->generateUrl( 'fellapp_file_download', array("id"=>$fellapp->getRecentReport()->getId()), true );

        $break = "\r\n";

        $text = "Dear " . $interviewer->getUsernameOptimal().",".$break.$break;
        $text .= "Please review the FELLOWSHIP INTERVIEW SCHEDULE for the candidate ".$applicant->getUsernameOptimal()." and submit your evaluation after the interview.".$break.$break;

        $text .= "The INTERVIEW SCHEDULE URL link:" . $break . $scheduleLink . $break.$break;

        $text .= "The ONLINE EVALUATION FORM URL link:" . $break . $interviewFormLink . $break.$break;

        $text .= "The COMPLETE APPLICATION PDF link:" . $break . $pdfLink . $break.$break;

        $text .= "If you have any additional questions, please don't hesitate to email " . $senderEmail . $break.$break;

        $logger->notice("sendInvitationEmail: Before send email to " . $email);

        if( $emailUtil == null ) {
            $emailUtil = new EmailUtil();
        }

        $cc = null; //"oli2002@med.cornell.edu";
        $emailUtil->sendEmail( $email, "Fellowship Candidate (".$applicant->getUsernameOptimal().") Interview Application and Evaluation Form", $text, $em, $cc, $senderEmail );

        $logger->notice("sendInvitationEmail: Email has been sent to " . $email);

        return $email;
    }

    public function sendConfirmationEmail( $emails, $fellapp, $event, $emailUtil, $request ) {

        if( $emails && count($emails) > 0 ) {
            $emailStr = " Emails have been sent to the following: ".implode(", ",$emails);
        } else {
            $emailStr = " Emails have not been sent.";
        }

        $logger = $this->container->get('logger');
        $em = $this->getDoctrine()->getManager();
        $userSecUtil = $this->container->get('user_security_utility');
        $systemUser = $userSecUtil->findSystemUser();
        $event = $event . $emailStr;
        $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,$fellapp,$request,'Fellowship Application Resend Emails');

        //return $this->redirect( $this->generateUrl('fellapp_home') );

        if( $emails && count($emails) > 0 ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );
        }

        //send only 1 email to coordinator
        $user = $this->get('security.context')->getToken()->getUser();
        $senderEmail = $user->getEmail();

        //get coordinator emails
        $coordinatorEmails = null;
        $fellappUtil = $this->container->get('fellapp_util');
        $coordinatorEmails = $fellappUtil->getCoordinatorsOfFellAppEmails($fellapp);

        //make sure current user get confirmation email too: insert it to coordinator emails
        if( $coordinatorEmails == null || !in_array($senderEmail, $coordinatorEmails) ) {
            $coordinatorEmails[] = $senderEmail;
        }

        $coordinatorEmails = implode(", ",$coordinatorEmails);
        //print_r($coordinatorEmails);
        //exit('1');

        if( $emailUtil == null ) {
            $emailUtil = new EmailUtil();
        }

        $applicant = $fellapp->getUser();
        $emailUtil->sendEmail( $coordinatorEmails, "Fellowship Candidate (".$applicant->getUsernameOptimal().") Interview Application and Evaluation Form", $event, $em, null, $senderEmail );

        $logger->notice("sendConfirmationEmail: Send confirmation email from " . $senderEmail . " to coordinators:".$coordinatorEmails);
    }



    /**
 * @Route("/invite-observers-to-view/{id}", name="fellapp_inviteobservers")
 * @Method("GET")
 */
    public function inviteObserversToRateAction(Request $request, $id) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') && false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //echo "invite interviewers to rate <br>";
        //exit();
        $logger = $this->container->get('logger');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }

        $emails = array();
        $emailUtil = new EmailUtil();

        //get all interviews
        $user = $this->get('security.context')->getToken()->getUser();
        $senderEmail = $user->getEmail();

        foreach( $entity->getObservers() as $observer ) {
            $pdfLink = $this->generateUrl( 'fellapp_file_download', array("id"=>$entity->getRecentReport()->getId()), true );

            //fellapp_file_download
            $scheduleLink = null;
            if( $entity->getRecentItinerary() ) {
                $scheduleDocumentId = $entity->getRecentItinerary()->getId();
                $scheduleLink = $this->generateUrl( 'fellapp_file_download', array("id"=>$scheduleDocumentId), true );
            }

            //get email
            $email = $observer->getEmail();
            $emails[] = $email;

            $applicant = $entity->getUser();

            $break = "\r\n";

            $text = "Dear " . $observer->getUsernameOptimal().",".$break.$break;
            $text .= "Please review the FELLOWSHIP APPLICATION for the candidate ".$applicant->getUsernameOptimal() . " (ID: ".$entity->getId().")".$break.$break;

            $text .= "The COMPLETE APPLICATION PDF link:" . $break . $pdfLink . $break.$break;

            if( $scheduleLink ) {
                $text .= "The INTERVIEW SCHEDULE URL link:" . $break . $scheduleLink . $break.$break;
            }

            $text .= "If you have any additional questions, please don't hesitate to email " . $senderEmail . $break.$break;

            $emailUtil->sendEmail( $email, "Fellowship Candidate (".$applicant->getUsernameOptimal().") Application", $text, $em, null, $senderEmail );

            $logger->notice("inviteObserversToRateAction: Send observer invitation email from " . $senderEmail . " to :".$email);
        }

        $event = "Invited observers to view fellowship application ID " . $id . ".";
        $this->sendConfirmationEmail($emails,$entity,$event,$emailUtil,$request);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode("ok"));
        return $response;
    }


    /**
     * @Route("/download-interview-applicants-list/{currentYear}/{fellappTypeId}/{fellappIds}", name="fellapp_download_interview_applicants_list")
     * @Method("GET")
     */
    public function downloadInterviewApplicantsListAction(Request $request, $currentYear, $fellappTypeId, $fellappIds) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') &&
            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') &&
            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_INTERVIEWER') &&
            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_OBSERVER')
        ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $fellowshipSubspecialty = null;
        $institutionNameFellappName = "";

        if( $fellappTypeId && $fellappTypeId > 0 ) {
            $fellowshipSubspecialty = $em->getRepository('OlegUserdirectoryBundle:FellowshipSubspecialty')->find($fellappTypeId);
        }

        if( $fellowshipSubspecialty ) {
            $institution = $fellowshipSubspecialty->getInstitution();
            $institutionNameFellappName = $institution." ".$fellowshipSubspecialty." ";
        }

        //url=http://collage.med.cornell.edu/order/fellowship-applications/show/2
        //$url = $this->generateUrl('fellapp_show',array('id' => 2),true);
        //echo "url=".$url."<br>";
        //exit();

        //[YEAR] [WCMC (top level of actual institution)] [FELLOWSHIP-TYPE] Fellowship Candidate Data generated on [DATE] at [TIME] EST.xls
        //"Interview Evaluations for FELLOWSHIP-TYPE YEAR generated for LoggedInUserFirstName LoggedInUserLastName on DATE TIME EST.docx
        $fileName = $currentYear." ".$institutionNameFellappName."Interview Evaluations generated on ".date('m/d/Y H:i').".pdf";
        $fileName = str_replace("  ", " ", $fileName);
        $fileName = str_replace(" ", "-", $fileName);

        $fellappUtil = $this->container->get('fellapp_util');

        //$docxBlob = $fellappUtil->createInterviewApplicantListDocx($fellappIds);

    if( 1 ) {


        $pageUrl = $this->generateUrl('fellapp_interview_applicants_list', array('fellappIds'=>$fellappIds), true); // use absolute path!


        $session = $this->get('session');
        $session->save();
        session_write_close();

        $PHPSESSID = $session->getId();

        $output = $this->get('knp_snappy.pdf')->getOutput($pageUrl, array(
            'cookie' => array(
                'PHPSESSID' => $PHPSESSID
            )));

        return new Response(
            $output,
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$fileName.'"'
            )
        );

        return new Response(
            $this->get('knp_snappy.pdf')->getOutput($pageUrl),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$fileName.'"'
            )
        );

    } else {

        $fellappUtil = $this->container->get('fellapp_util');
        $entities = $fellappUtil->createInterviewApplicantList( $fellappIds );

        $html = $this->renderView('OlegFellAppBundle:Interview:applicants-interview-info.html.twig', array(
            'entities' => $entities,
            'pathbase' => 'fellapp',
            'cycle' => 'show',
            'sitename' => $this->container->getParameter('fellapp.sitename')
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$fileName.'"'
            )
        );
    }

//        $response = new Response();
//        $response->headers->set('Content-Type', 'application/vnd.ms-word');
//        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
//        $response->setContent($docxBlob);
//        return $response;
//
//        //$writer = \PhpOffice\PhpWord\IOFactory::createWriter($docxBlob, 'HTML');
//        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($docxBlob, 'Word2007');
//
//        header("Content-Description: File Transfer");
//        header('Content-Disposition: attachment; filename="' . $fileName . '"');
//
//        //application/msword
//        //application/vnd.openxmlformats-officedocument.wordprocessingml.document
//        //header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//        //header('Content-Type: application/msword');
//        header('Content-Type: application/pdf');
//
//        //header('Content-Transfer-Encoding: binary');
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Expires: 0');
//
//
//        // Write file to the browser
//        $writer->save('php://output');
//
//        exit();
    }


    /**
     * @Route("/interview-applicants-list/{fellappIds}", name="fellapp_interview_applicants_list")
     * @Method("GET")
     * @Template("OlegFellAppBundle:Interview:applicants-interview-info.html.twig")
     */
    public function showInterviewApplicantsListAction(Request $request, $fellappIds) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') &&
            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') &&
            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_INTERVIEWER') &&
            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_OBSERVER')
        ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $userSecUtil = $this->container->get('user_security_utility');
        $logger = $this->container->get('logger');

        if(0){
        $user = $this->get('security.context')->getToken()->getUser();
        //download link can be accessed by a console as localhost with role IS_AUTHENTICATED_ANONYMOUSLY, so simulate login manually
        if( !($user instanceof User) ) {
            $firewall = 'ldap_fellapp_firewall';
            $systemUser = $userSecUtil->findSystemUser();
            if( $systemUser ) {
                $token = new UsernamePasswordToken($systemUser, null, $firewall, $systemUser->getRoles());
                $this->get('security.context')->setToken($token);
                //$this->get('security.token_storage')->setToken($token);
            }
            $logger->notice("showInterviewApplicantsListAction: Logged in as systemUser=".$systemUser);
        } else {
            $logger->notice("showInterviewApplicantsListAction: Token user is valid security.context user=".$user);
        }
        }//if

        $fellappUtil = $this->container->get('fellapp_util');

        $entities = $fellappUtil->createInterviewApplicantList( $fellappIds );

        return array(
            'entities' => $entities,
            'pathbase' => 'fellapp',
            'cycle' => 'show',
            'sitename' => $this->container->getParameter('fellapp.sitename')
        );
    }

} 