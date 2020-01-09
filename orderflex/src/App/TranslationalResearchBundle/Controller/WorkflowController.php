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

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 8/24/2017
 * Time: 4:41 PM
 */

namespace App\TranslationalResearchBundle\Controller;


use App\TranslationalResearchBundle\Entity\Project;
use App\TranslationalResearchBundle\Entity\TransResRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;

class WorkflowController extends Controller
{

    /**
     * Dump Workflows
     *
     * @Route("/workflow/{type}", name="translationalresearch_workflow_show")
     * @Template("AppTranslationalResearchBundle/Workflow/workflow.html.twig")
     */
    public function dumpWorkflowAction(Request $request, $type)
    {
//        $definitionBuilder = new DefinitionBuilder();
//        $definition = $definitionBuilder->addPlaces(['draft', 'review', 'rejected', 'published'])
//            // Transitions are defined with a unique name, an origin place and a destination place
//            ->addTransition(new Transition('to_review', 'draft', 'review'))
//            ->addTransition(new Transition('publish', 'review', 'published'))
//            ->addTransition(new Transition('reject', 'review', 'rejected'))
//            ->build()
//        ;
//
//        $dumper = new GraphvizDumper();
//        //echo $dumper->dump($definition);
//
//        $graphviz = new GraphViz();
//        $graphviz->display($dumper);

        //$file = 'semitransparent.png'; // path to png image
        //$img = imagecreatefrompng($file); // open image

        //png is generated by Graphviz2.38:
        //1) open windows "Command Prompt"
        //2) run: > php bin/console workflow:dump transres_project | "C:\Program Files (x86)\Graphviz2.38\bin\dot.exe" -Tpng -o graph.png
        //3 workflow (first open cmd go to the C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\scanorder\Scanorders2):
        //php bin/console workflow:dump transres_project | "C:\Program Files (x86)\Graphviz2.38\bin\dot.exe" -Tpng -o project.png
        //php bin/console workflow:dump transres_request_billing | "C:\Program Files (x86)\Graphviz2.38\bin\dot.exe" -Tpng -o request_billing.png
        //php bin/console workflow:dump transres_request_progress | "C:\Program Files (x86)\Graphviz2.38\bin\dot.exe" -Tpng -o request_progress.png
        //place the rsulting pngs to scanorder\Scanorders2\src\App\TranslationalResearchBundle\Resources\public\images

        $filename = null;

        if( $type == "project-requests" ) {
            $title = "Project Request Workflow";
            $filename = "project.png";
            $this->makeAndRunCmdAsync($type,$filename);
        }
        if( $type == "work-requests-billing" ) {
            $title = "Work Request Billing Progress Workflow";
            $filename = "request_billing.png";
            $this->makeAndRunCmdAsync($type,$filename);
        }
        if( $type == "work-requests-completion-progress" ) {
            $title = "Work Request Completion Progress Workflow";
            $filename = "request_progress.png";
            $this->makeAndRunCmdAsync($type,$filename);
        }

        if( !$filename ) {
            exit("Filename with workflow graph is not specified.");
        }

//        $output = null;
//        $return = null;
//        $shellout = exec( "cd", $output, $return );
//        echo "shellout=".$shellout."<br>";
//        echo "output=".$output."<br>";
//        echo "return=".$return."<br>";

        //$shellout = system("cd");
        //echo "<br>shellout=".$shellout."<br>";

        //exit("EXIT: type=".$type."; filename=".$filename);

//        $webpath = $this->get('kernel')->getRootDir();
        //echo "webPath=$webpath<br>";
        //exit();
//        $file = $webpath."/../src/App/TranslationalResearchBundle/Resources/public/images/".$filename;
//        if (!file_exists($file)) {
//            exit("File does not exist filename=".$filename);
//        }

        //$bundleFileName = '@AppTranslationalResearchBundle/Resources/public/images/'.$filename;
        $bundleFileName = "bundles\\olegtranslationalresearch\\images\\".$filename;

//        $imagename = "Project Workflow";
//
//        header("Content-Type: image/png");
//        header('Content-Length: ' . filesize($file));
//        header("Content-Disposition: inline; filename='$imagename'");
//        readfile($file);
//        exit(0);

        return array(
            //'file' => $file,
            'bundleFileName' => $bundleFileName,
            'fileName' => $filename,
            'title' => $title
        );
    }

    //previously windowsOsCmdRunAsync
    public function makeAndRunCmdAsync($type,$filename) {

        return;

        $cmd = "php ..".DIRECTORY_SEPARATOR."bin".DIRECTORY_SEPARATOR."console workflow:dump " . 'transres_'.$type . " | " .
            "C:".DIRECTORY_SEPARATOR."Program Files (x86)".DIRECTORY_SEPARATOR."Graphviz2.38".
            DIRECTORY_SEPARATOR."bin".DIRECTORY_SEPARATOR."dot.exe -Tpng -o " . $filename;

        echo "cmd=".$cmd."<br>";

        $dir = $this->get('kernel')->getRootDir();
        echo "dir=".$dir."<br>";

        $oExec = null;
        //$WshShell = new \COM("WScript.Shell");
        //$oExec = $WshShell->Run($cmd, 0, false);

        //$oExec = pclose(popen("start ". $cmd, "r"));
        //$oExec = pclose(popen("start /B ". $cmd, "r"));
        //$oExec = exec($cmd);
        //$oExec = system($cmd);

        $userServiceUtil = $this->container->get('user_service_utility');
        $oExec = $userServiceUtil->execInBackground($cmd);

        //$logger = $this->container->get('logger');
        echo "Cmd Run Sync: oExec=".$oExec."<br>";

        return $oExec;
    }


    /**
     * NOT USED - this is a particulra case for testing
     * https://symfony.com/doc/current/workflow/usage.html
     *
     * @Route("/to-irb-review/{id}", name="translationalresearch_to_irb_review")
     * @Method("GET")
     */
    public function toIrbReviewAction( Project $project )
    {
        $transresUtil = $this->container->get('transres_util');
        $workflow = $this->container->get('state_machine.transres_project');

        $workflow->can($project, 'to_irb_review'); // True
        $workflow->can($project, 'to_admin_review'); // False

        // Update the currentState on the post
        if( $workflow->can($project, 'to_irb_review') ) {
            $human = $this->getHumanName();
            try {
                $workflow->apply($project, 'to_irb_review');
                //change state
                $project->setState('irb_review');
                $this->addFlash(
                    'error',
                    "change state to $human Review OK."
                );
            } catch (LogicException $e) {
                $this->addFlash(
                    'error',
                    "change state to $human Review failed."
                );
            }
        }

        // See all the available transition for the post in the current state
        $transitions = $workflow->getEnabledTransitions($project);
        echo "<pre>";
        print_r($transitions);
        echo "</pre><br><br>";

        foreach( $transitions as $transition ) {
//            echo $transition->getName().": ";
//            $froms = $transition->getFroms();
//            foreach( $froms as $from ) {
//                echo "from=".$from.", ";
//            }
//            $tos = $transition->getTos();
//            foreach( $tos as $to ) {
//                echo "to=".$to.", ";
//            }
//            echo "<br>";
            $transresUtil->printTransition($transition);
        }

        exit();
        return $this->redirectToRoute('translationalresearch_home');
    }


    /**
     * NOT USED
     * https://symfony.com/doc/current/workflow/usage.html
     *
     * @Route("/project-transition-state/{transitionName}/{to}/{id}", name="translationalresearch_transition_state_action")
     * @Method("GET")
     */
    public function transitionStateAction( $transitionName, $to, Project $project )
    {
        $transresUtil = $this->container->get('transres_util');
        $workflow = $this->container->get('state_machine.transres_project');

        //$workflow->can($project, 'to_irb_review'); // True
        //$workflow->can($project, 'to_admin_review'); // False

//        //Get Transition and $to
//        $transition = $transresUtil->getTransitionByName($project,$transitionName);
//        $tos = $transition->getTos();
//        if( count($tos) != 1 ) {
//            throw $this->createNotFoundException('Available to state is not a single state; count='.$tos.": ".implode(",",$tos));
//        }
//        $to = $tos[0];

        // Update the currentState on the post
        if( $workflow->can($project, $transitionName) ) {
            try {
                $workflow->apply($project, $transitionName);
                //change state
                $project->setState($to); //i.e. 'irb_review'
                $this->addFlash(
                    'error',
                    "Successfully changed state to $to"
                );
            } catch (LogicException $e) {
                $this->addFlash(
                    'error',
                    "Change state to $to failed"
                );
            }
        }

        // See all the available transition for the post in the current state
//        $transitions = $workflow->getEnabledTransitions($project);
//        echo "<pre>";
//        print_r($transitions);
//        echo "</pre><br><br>";
//        foreach( $transitions as $transition ) {
//            $transresUtil->printTransition($transition);
//        }

        //exit();
        return $this->redirectToRoute('translationalresearch_home');
    }

    /**
     * Change state of the project (by id) and make transition to this place indicated by transitionName
     * https://symfony.com/doc/current/workflow/usage.html
     *
     * @Route("/project-transition/{transitionName}/{id}", name="translationalresearch_transition_action")
     * @Method("GET")
     */
    public function transitionAction( $transitionName, Project $project )
    {
        exit("NOT USED PATH: translationalresearch_transition_action");

        $transresUtil = $this->container->get('transres_util');
        $transresUtil->setTransition($project,null,$transitionName);

        //exit();
        return $this->redirectToRoute('translationalresearch_home');
    }

    /**
     * Change state of the project (by id) and make transition to this place indicated by transitionName
     * https://symfony.com/doc/current/workflow/usage.html
     *
     * @Route("/project-review-transition/{transitionName}/{id}/{reviewId}", name="translationalresearch_transition_action_by_review")
     * @Method("GET")
     */
    public function transitionReviewAction( $transitionName, Project $project, $reviewId )
    {
        $transresUtil = $this->container->get('transres_util');
        $review = $transresUtil->getReviewByReviewidAndState($reviewId,$project->getState());

        if(
            $transresUtil->isUserAllowedReview($review) === false &&
            $transresUtil->isReviewCorrespondsToState($review) === false &&
            $transresUtil->isUserAllowedFromThisStateByProjectOrReview($project) === false
        ) {
            //exit("no permission");
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        //echo $review->getId().": transitionName=".$transitionName."<br>";
        //exit();

        $transresUtil = $this->container->get('transres_util');

        $to = null;
        $testing = false;
        //$testing = true;

        $transresUtil->setTransition($project,$review,$transitionName,$to,$testing);

        if( $testing ) {
            exit();
        }

        return $this->redirectToRoute('translationalresearch_home');
    }


    /**
     * Change state of the request (by id) and make transition to this place indicated by transitionName
     * https://symfony.com/doc/current/workflow/usage.html
     *
     * @Route("/request-review-transition/{transitionName}/{id}/{statMachineType}", name="translationalresearch_request_transition_action_by_review")
     * @Method("GET")
     */
    public function transitionRequestReviewAction( $transitionName, TransResRequest $transresRequest, $statMachineType )
    {
        $transresUtil = $this->container->get('transres_util');
        $transresRequestUtil = $this->container->get('transres_request_util');

        $project = $transresRequest->getProject();

        if(
            $transresUtil->isAdminOrPrimaryReviewer($project->getProjectSpecialty()) === false &&
            $transresRequestUtil->isRequestStateReviewer($transresRequest,$statMachineType) === false
        ) {
            //exit("no permission");
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        //echo $transresRequest->getId().": transitionName=".$transitionName."<br>";
        //exit();

        $to = null;
        $testing = false;
        //$testing = true;

        $transresRequestUtil->setRequestTransition($transresRequest,$statMachineType,$transitionName,$to,$testing);

        //exit();
        return $this->redirectToRoute('translationalresearch_request_index',array('id'=>$project->getId()));
    }

}