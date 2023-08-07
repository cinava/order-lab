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

namespace App\OrderformBundle\Controller;



use App\OrderformBundle\Entity\Educational;
use App\OrderformBundle\Entity\Message; //process.py script: replaced namespace by ::class: added use line for classname=Message
use App\OrderformBundle\Entity\Research;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\UserdirectoryBundle\Controller\OrderAbstractController;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Routing\Annotation\Route;


class DataReviewController extends OrderAbstractController {
      

    #[Route(path: '/scan-order/{id}/data-review', name: 'scan-order-data-review-full', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[Template('AppOrderformBundle/DataReview/index-order.html.twig')]
    public function getDataReviewAction($id) {

        $em = $this->getDoctrine()->getManager();

        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:Message'] by [Message::class]
        $message = $em->getRepository(Message::class)->findOneByOid($id);

        $queryE = $em->createQueryBuilder()
            //->from('AppOrderformBundle:Educational', 'e')
            ->from(Educational::class, 'e')
            ->select("e")
            ->leftJoin("e.message", "message")
            ->where("message.id=:id")
            ->setParameter("id",$id);

        $educational = $queryE->getQuery()->getResult();


        $queryR = $em->createQueryBuilder()
            //->from('AppOrderformBundle:Research', 'e')
            ->from(Research::class, 'e')
            ->select("e")
            ->leftJoin("e.message", "message")
            ->where("message.id=:id")
            ->setParameter("id",$id);

        $research = $queryR->getQuery()->getResult();

        return array(
            'educationals' => $educational,
            'researches' => $research,
            'entity' => $message
        );

    }


}
