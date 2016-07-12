<?php
/**
 * Created by PhpStorm.
 * User: DevServer
 * Date: 8/20/15
 * Time: 4:21 PM
 */

namespace Oleg\FellAppBundle\Util;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Oleg\FellAppBundle\Entity\DataFile;
use Oleg\FellAppBundle\Entity\Interview;
use Oleg\UserdirectoryBundle\Entity\AccessRequest;
use Oleg\UserdirectoryBundle\Entity\BoardCertification;
use Oleg\UserdirectoryBundle\Entity\Citizenship;
use Oleg\UserdirectoryBundle\Entity\Document;
use Oleg\UserdirectoryBundle\Entity\EmploymentStatus;
use Oleg\UserdirectoryBundle\Entity\Examination;
use Oleg\FellAppBundle\Entity\FellowshipApplication;
use Oleg\UserdirectoryBundle\Entity\GeoLocation;
use Oleg\UserdirectoryBundle\Entity\JobTitleList;
use Oleg\UserdirectoryBundle\Entity\Location;
use Oleg\FellAppBundle\Entity\Reference;
use Oleg\UserdirectoryBundle\Entity\StateLicense;
use Oleg\UserdirectoryBundle\Entity\Training;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use Oleg\UserdirectoryBundle\Util\EmailUtil;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class FellAppUtil {

    protected $em;
    protected $sc;
    protected $container;

    protected $systemEmail;


    public function __construct( $em, $sc, $container ) {

        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;

    }



    //check for active access requests
    public function getActiveAccessReq() {
        if( !$this->sc->isGranted('ROLE_FELLAPP_COORDINATOR') ) {
            //exit('not granted ROLE_FELLAPP_COORDINATOR ???!!!'); //testing
            return null;
        } else {
            //exit('granted ROLE_FELLAPP_COORDINATOR !!!'); //testing
        }
        $userSecUtil = $this->container->get('user_security_utility');
        $accessreqs = $userSecUtil->getUserAccessRequestsByStatus($this->container->getParameter('fellapp.sitename'),AccessRequest::STATUS_ACTIVE);
        return $accessreqs;
    }

    //$fellSubspecArg: single fellowshipSubspecialty id or array of fellowshipSubspecialty ids
    public function getFellAppByStatusAndYear($status,$fellSubspecArg,$year=null,$interviewer=null) {

        $repository = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication');
        $dql =  $repository->createQueryBuilder("fellapp");
        $dql->select('fellapp');
        $dql->leftJoin("fellapp.appStatus", "appStatus");

        if( strpos($status, "-") !== false ) {
            $statusArr = explode("-", $status);
            $statusStr = $statusArr[0];
            $statusNot = $statusArr[1];
            if( $statusNot && $statusNot == 'not' ) {
                //'interviewee-not' is dummy status which is all statuses but not
                $dql->where("appStatus.name != '" . $statusStr . "'");
            }
        } else {
            $dql->where("appStatus.name = '" . $status . "'");
        }

        if( $fellSubspecArg ) {
            $dql->leftJoin("fellapp.fellowshipSubspecialty","fellowshipSubspecialty");
            if( is_array($fellSubspecArg) ) {
                $felltypeArr = array();
                foreach( $fellSubspecArg as $fellowshipTypeID => $fellowshipTypeName ) {
                    $felltypeArr[] = "fellowshipSubspecialty.id = ".$fellowshipTypeID;
                }
                $dql->andWhere( implode(" OR ", $felltypeArr) );
            } else {
                $dql->andWhere("fellowshipSubspecialty.id=".$fellSubspecArg);
            }
        }

        if( $year ) {
            $bottomDate = $year."-01-01";
            $topDate = $year."-12-31";
            $dql->andWhere("fellapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );
        }

        if( $interviewer ) {
            $dql->leftJoin("fellapp.interviews", "interviews");
            $dql->leftJoin("interviews.interviewer", "interviewer");
            $dql->andWhere("interviewer.id=".$interviewer->getId());
        }

        //echo "dql=".$dql."<br>";

        $query = $this->em->createQuery($dql);
        $applicants = $query->getResult();

        return $applicants;
    }

//    public function getFellAppByUserAndStatusAndYear($subjectUser, $status,$fellSubspecId,$year=null) {
//
//        $repository = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication');
//        $dql =  $repository->createQueryBuilder("fellapp");
//        $dql->select('fellapp');
//        $dql->leftJoin("fellapp.appStatus", "appStatus");
//        $dql->where("appStatus.name = '" . $status . "'");
//
//        if( $fellSubspecId ) {
//            $dql->leftJoin("fellapp.fellowshipSubspecialty","fellowshipSubspecialty");
//            $dql->andWhere("fellowshipSubspecialty.id=".$fellSubspecId);
//        }
//
//        if( $year ) {
//            $bottomDate = "01-01-".$year;
//            $topDate = "12-31-".$year;
//            $dql->andWhere("fellapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );
//        }
//
//        if( $subjectUser ) {
//            $dql->leftJoin("fellapp.interviews", "interviews");
//            $dql->andWhere("interviews.interviewer=".$subjectUser);
//        }
//
//        $query = $this->em->createQuery($dql);
//        $applicants = $query->getResult();
//
//        return $applicants;
//    }

    //get fellowship types based on the user roles
    public function getFellowshipTypesByUser( $user ) {
        $em = $this->em;
        $userSecUtil = $this->container->get('user_security_utility');

        if( $userSecUtil->hasGlobalUserRole( "ROLE_FELLAPP_ADMIN", $user ) ) {
            return $this->getFellowshipTypesByInstitution();
        }

        $filterTypes = array();
        $filterTypeIds = array();

        foreach( $user->getRoles() as $rolename ) {
            $roleObject = $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName($rolename);
            if( $roleObject ) {
                $fellowshipSubspecialty = $roleObject->getFellowshipSubspecialty();
                if( $fellowshipSubspecialty ) {
                    $filterTypes[$fellowshipSubspecialty->getId()] = $fellowshipSubspecialty->getName();
                    $filterTypeIds[] = $fellowshipSubspecialty->getId();
                }
            }
        }

//        if( count($filterTypes) > 1 ) {
//            $filterTypes[implode(";",$filterTypeIds)] = "ALL";
//        }

        //$filterTypes = array_reverse($filterTypes);

        return $filterTypes;
    }

    //get all fellowship application types using role
    public function getFellowshipTypesByInstitution($asEntities=false) {
        $em = $this->em;

        $mapper = array(
            'prefix' => 'Oleg',
            'bundleName' => 'UserdirectoryBundle',
            'className' => 'Institution'
        );

        $wcmc = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("WCMC");
        $pathology = $em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent(
            "Pathology and Laboratory Medicine",
            $wcmc,
            $mapper
        );

        //get list of fellowship type with extra "ALL"
        $repository = $em->getRepository('OlegUserdirectoryBundle:FellowshipSubspecialty');
        $dql = $repository->createQueryBuilder('list');
        $dql->leftJoin("list.institution","institution");
        $dql->where("institution.id = ".$pathology->getId());
        $dql->orderBy("list.orderinlist","ASC");

        $query = $em->createQuery($dql);

        $fellTypes = $query->getResult();
        //echo "fellTypes count=".count($fellTypes)."<br>";

        if( $asEntities ) {
            return $fellTypes;
        }

        //add statuses
        foreach( $fellTypes as $type ) {
            //echo "type: id=".$status->getId().", name=".$status->getName()."<br>";
            $filterType[$type->getId()] = $type->getName();
        }

        return $filterType;
    }

//    public function getFellowshipTypesWithSpecials_OLD() {
//        $em = $this->em;
//
//        //get list of fellowship type with extra "ALL"
//        $repository = $em->getRepository('OlegUserdirectoryBundle:FellowshipSubspecialty');
//        $dql = $repository->createQueryBuilder('list');
//        //$dql->select("list.id as id, list.name as text")
//        $dql->leftJoin("list.parent","parent");
//        $dql->where("list.type = :typedef OR list.type = :typeadd");
//        $dql->andWhere("parent.name LIKE '%Pathology%' OR parent.name LIKE '%Clinical Molecular Genetics%' OR parent IS NULL");
//        //$dql->andWhere("parent.name LIKE '%Pathology%'");
//        $dql->orderBy("list.orderinlist","ASC");
//
//        $query = $em->createQuery($dql);
//
//        $query->setParameters( array(
//            'typedef' => 'default',
//            'typeadd' => 'user-added',
//            //'parentName' => 'Pathology'
//        ));
//
//        $fellTypes = $query->getResult();
//
//        //add special cases
////        $specials = array(
////            "ALL" => "ALL",
////        );
//
////        $filterType = array();
////        foreach( $specials as $key => $value ) {
////            $filterType[$key] = $value;
////        }
//
//        //add statuses
//        foreach( $fellTypes as $type ) {
//            //echo "type: id=".$status->getId().", name=".$status->getName()."<br>";
//            $filterType[$type->getId()] = $type->getName();
//        }
//
//        return $filterType;
//    }


    //check if the user can view this fellapp application
    public function hasFellappPermission( $user, $fellapp ) {

        $res = false;

        //if user is observer of this fellapp
        if( $fellapp->getObservers()->contains($user) ) {
            $res = true;
        }

        //echo "res=".$res."<br>";

        //if user has the same fellapp type as this fellapp
        if( $this->hasSameFellowshipTypeId($user, $fellapp->getFellowshipSubspecialty()->getId()) ) {
            $res = true;
        }

        //echo "res=".$res."<br>";
        //exit('1');

        return $res;
    }

    //check fellowship types based on the user roles
    public function hasSameFellowshipTypeId( $user, $felltypeid ) {
        $em = $this->em;
        $userSecUtil = $this->container->get('user_security_utility');

        if( $userSecUtil->hasGlobalUserRole( "ROLE_FELLAPP_ADMIN", $user ) ) {
            return true;
        }

        //echo "felltypeid=".$felltypeid."<br>";

        foreach( $user->getRoles() as $rolename ) {
            $roleObject = $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName($rolename);
            if( $roleObject ) {
                $fellowshipSubspecialty = $roleObject->getFellowshipSubspecialty();
                if( $fellowshipSubspecialty ) {
                    if( $felltypeid == $fellowshipSubspecialty->getId() ) {
                        //it is safer to check also for fellowshipSubspecialty's institution is under roleObject's institution
                        if( $em->getRepository('OlegUserdirectoryBundle:Institution')->isNodeUnderParentnode( $roleObject->getInstitution(), $fellowshipSubspecialty->getInstitution() ) ) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function getCoordinatorsOfFellAppEmails($fellapp) {
        return $this->getEmailsOfFellApp( $fellapp, "_COORDINATOR_" );
    }
    public function getDirectorsOfFellAppEmails($fellapp) {
        return $this->getEmailsOfFellApp( $fellapp, "_DIRECTOR_" );
    }
    public function getCoordinatorsOfFellApp( $fellapp ) {
        return $this->getUsersOfFellAppByRole( $fellapp, "_COORDINATOR_" );
    }
    public function getDirectorsOfFellApp( $fellapp ) {
        return $this->getUsersOfFellAppByRole( $fellapp, "_DIRECTOR_" );
    }

    //get coordinator of given fellapp
    public function getUsersOfFellAppByRole( $fellapp, $roleName ) {

        if( !$fellapp ) {
            return null;
        }

        $em = $this->em;

        $fellowshipSubspecialty = $fellapp->getFellowshipSubspecialty();
        //echo "fellowshipSubspecialty=".$fellowshipSubspecialty."<br>";

        if( !$fellowshipSubspecialty ) {
            return null;
        }

        $coordinatorFellTypeRole = null;

        $roles = $em->getRepository('OlegUserdirectoryBundle:Roles')->findByFellowshipSubspecialty($fellowshipSubspecialty);
        foreach( $roles as $role ) {
            if( strpos($role,$roleName) !== false ) {
                $coordinatorFellTypeRole = $role;
                break;
            }
        }

        $users = $em->getRepository('OlegUserdirectoryBundle:User')->findUserByRole($coordinatorFellTypeRole);

        return $users;
    }

    public function getEmailsOfFellApp( $fellapp, $roleName ) {

        $users = $this->getUsersOfFellAppByRole( $fellapp, $roleName );

        $emails = array();
        if( $users && count($users) > 0 ) {
            foreach( $users as $user ) {
                $emails[] = $user->getEmail();
            }
        }

        //echo "coordinator emails<br>";
        //print_r($emails);
        //exit('1');

        return $emails;
    }

    //send confirmation email to the corresponding Fellowship director and coordinator
    public function sendConfirmationEmailsOnApplicationPopulation( $fellowshipApplication, $applicant ) {
        $fellappUtil = $this->container->get('fellapp_util');
        $logger = $this->container->get('logger');

        $directorEmails = $fellappUtil->getDirectorsOfFellAppEmails($fellowshipApplication);
        $coordinatorEmails = $fellappUtil->getCoordinatorsOfFellAppEmails($fellowshipApplication);
        $responsibleEmails = array_unique (array_merge ($coordinatorEmails, $directorEmails));
        $logger->notice("Send confirmation email (fellowship application ".$fellowshipApplication->getId()." populated in DB) to the directors and coordinators emails " . implode(", ",$responsibleEmails));

        //[FellowshipType Fellowship] FirstNameOfApplicant LastNameOfApplicant's application received
        $populatedSubjectFellApp = "[".$fellowshipApplication->getFellowshipSubspecialty()." Fellowship] ".$applicant->getUsernameShortest()."'s application received";

        /////////////// Configuring the Request Context per Command ///////////////
        // http://symfony.com/doc/current/cookbook/console/request_context.html
        $userSecUtil = $this->container->get('user_security_utility');
        $liveSiteRootUrl = $userSecUtil->getSiteSettingParameter('liveSiteRootUrl');    //http://c.med.cornell.edu/order/
        $liveSiteHost = parse_url($liveSiteRootUrl, PHP_URL_HOST); //c.med.cornell.edu
        //echo "liveSiteHost=".$liveSiteHost."; ";
        $context = $this->container->get('router')->getContext();
        $context->setHost($liveSiteHost);
        $context->setScheme('http');
        $context->setBaseUrl('/order');
        /////////////// EOF Configuring the Request Context per Command ///////////////

        //FirstNameOfApplicant LastNameOfApplicant has submitted a new application to your FellowshipType StartDate'sYear(suchAs2018) fellowship
        // on SubmissionDate and you can access it here: LinkToGeneratedApplicantPDF.
        //To mark this application as priority, please click the following link and log in if prompted:
        //LinkToChangeStatusOfApplicationToPriority
        $linkToGeneratedApplicantPDF = $this->container->get('router')->generate(
            'fellapp_view_pdf',
            array(
                'id' => $fellowshipApplication->getId()
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        //echo "linkToGeneratedApplicantPDF=".$linkToGeneratedApplicantPDF."; ";

        $linkToChangeStatusOfApplicationToPriority = $this->container->get('router')->generate(
            'fellapp_status_email',
            array(
                'id' => $fellowshipApplication->getId(),
                'status' => 'priority'
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $linkToList = $this->container->get('router')->generate(
            'fellapp_home',
            array(
                'startDate' => $fellowshipApplication->getStartDate()->format('Y'), //2018
                'filter' => $fellowshipApplication->getFellowshipSubspecialty()->getId()
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $creationDate = $fellowshipApplication->getCreatedate();
        $creationDate->setTimezone(new \DateTimeZone('America/New_York'));
        $creationDateStr = $creationDate->format('m/d/Y h:i A T');

        $break = "\r\n";
        $populatedBodyFellApp = $applicant->getUsernameShortest()." has submitted a new application to your ".$fellowshipApplication->getFellowshipSubspecialty().
            " ".$fellowshipApplication->getStartDate()->format('Y')."'s fellowship on ".$creationDateStr.
            " and you can access it here: ".$break.$linkToGeneratedApplicantPDF;
        $populatedBodyFellApp .= $break.$break."To mark this application as priority, please click the following link and log in if prompted:".
            $break.$linkToChangeStatusOfApplicationToPriority;

        //To view the list of all received FellowshipType FellowshipYear applications, please follow this link:
        $populatedBodyFellApp .= $break.$break."To view the list of all received ".
            $fellowshipApplication->getFellowshipSubspecialty()." ".$fellowshipApplication->getStartDate()->format('Y')." applications, please follow this link:".$break;
        $populatedBodyFellApp .= $linkToList;

        //If you are off site, please connect via VPN first ( https://its.weill.cornell.edu/services/wifi-networks/vpn ) and then follow the links above.
        $populatedBodyFellApp .= $break.$break."If you are off site, please connect via VPN first (https://webvpn.med.cornell.edu/) and then follow the links above.";

        $emailUtil = $this->container->get('user_mailer_utility');
        $emailUtil->sendEmail( $responsibleEmails, $populatedSubjectFellApp, $populatedBodyFellApp );
    }

    public function addDefaultInterviewers( $fellapp ) {

        $fellowshipSubspecialty = $fellapp->getFellowshipSubspecialty();

        foreach( $fellowshipSubspecialty->getInterviewers() as $interviewer ) {

            if( $this->isInterviewerExist($fellapp,$interviewer) == false ) {
                $interview = new Interview();
                $interview->setInterviewer($interviewer);
                $interview->setLocation($interviewer->getMainLocation());
                $interview->setInterviewDate($fellapp->getInterviewDate());
                $fellapp->addInterview($interview);
            }

        }

    }

    public function isInterviewerExist( $fellapp, $interviewer ) {
        foreach( $fellapp->getInterviews() as $interview ) {
            if( $interview->getInterviewer()->getId() == $interviewer->getId() ) {
                return true;
            }
        }
        return false;
    }







    public function addEmptyFellAppFields($fellowshipApplication) {

        $em = $this->em;
        //$userSecUtil = $this->container->get('user_security_utility');
        //$systemUser = $userSecUtil->findSystemUser();
        $user = $fellowshipApplication->getUser();
        $author = $this->sc->getToken()->getUser();

        //Pathology Fellowship Applicant in EmploymentStatus
        $employmentType = $em->getRepository('OlegUserdirectoryBundle:EmploymentType')->findOneByName("Pathology Fellowship Applicant");
        if( !$employmentType ) {
            throw new EntityNotFoundException('Unable to find entity by name='."Pathology Fellowship Applicant");
        }
        if( count($user->getEmploymentStatus()) == 0 ) {
            $employmentStatus = new EmploymentStatus($author);
            $employmentStatus->setEmploymentType($employmentType);
            $user->addEmploymentStatus($employmentStatus);
        }

        //locations
        $this->addEmptyLocations($fellowshipApplication);

        //Education
        $this->addEmptyTrainings($fellowshipApplication);

        //National Boards: oleg_fellappbundle_fellowshipapplication_examinations_0_USMLEStep1DatePassed
        $this->addEmptyNationalBoards($fellowshipApplication);

        //Medical Licensure: oleg_fellappbundle_fellowshipapplication[stateLicenses][0][licenseNumber]
        $this->addEmptyStateLicenses($fellowshipApplication);

        //Board Certification
        $this->addEmptyBoardCertifications($fellowshipApplication);

        //References
        $this->addEmptyReferences($fellowshipApplication);

    }


    //oleg_fellappbundle_fellowshipapplication_references_0_name
    public function addEmptyReferences($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();
        $references = $fellowshipApplication->getReferences();
        $count = count($references);

        //must be 4
        //Remove the fourth letter of recommendation from the front end application form => 3 references
        for( $count; $count < 3; $count++  ) {

            $reference = new Reference($author);
            $fellowshipApplication->addReference($reference);

        }

    }

    public function addEmptyBoardCertifications($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();
        $boardCertifications = $fellowshipApplication->getBoardCertifications();
        $count = count($boardCertifications);

        //must be 3
        for( $count; $count < 3; $count++  ) {

            $boardCertification = new BoardCertification($author);
            $fellowshipApplication->addBoardCertification($boardCertification);
            $fellowshipApplication->getUser()->getCredentials()->addBoardCertification($boardCertification);

        }

    }

    //oleg_fellappbundle_fellowshipapplication[stateLicenses][0][licenseNumber]
    public function addEmptyStateLicenses($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();

        $stateLicenses = $fellowshipApplication->getStateLicenses();

        $count = count($stateLicenses);

        //must be 2
        for( $count; $count < 2; $count++  ) {

            $license = new StateLicense($author);
            $fellowshipApplication->addStateLicense($license);
            $fellowshipApplication->getUser()->getCredentials()->addStateLicense($license);

        }

    }

    public function addEmptyNationalBoards($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();

        $examinations = $fellowshipApplication->getExaminations();

        if( count($examinations) == 0 ) {
            $examination = new Examination($author);
            $fellowshipApplication->addExamination($examination);
        } else {
            //$examination = $examinations[0];
        }

    }


    public function addEmptyLocations($fellowshipApplication) {

        $this->addLocationByType($fellowshipApplication,"Present Address");
        $this->addLocationByType($fellowshipApplication,"Permanent Address");
        $this->addLocationByType($fellowshipApplication,"Work Address");

    }
    public function addLocationByType($fellowshipApplication,$typeName) {

        $user = $fellowshipApplication->getUser();

        $specificLocation = null;

        foreach( $user->getLocations() as $location ) {
            if( $location->hasLocationTypeName($typeName) ) {
                $specificLocation = $location;
                break;
            }
        }

        if( !$specificLocation ) {

            $locationType = $this->em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName($typeName);
            if( !$locationType ) {
                throw new EntityNotFoundException('Unable to find entity by name='.$typeName);
            }

            $specificLocation = new Location();
            $specificLocation->setName('Fellowship Applicant '.$typeName);
            $specificLocation->addLocationType($locationType);
            $user->addLocation($specificLocation);
            $fellowshipApplication->addLocation($specificLocation);
        }

    }

    public function addEmptyTrainings($fellowshipApplication) {

        //set TrainingType
        $this->addTrainingByType($fellowshipApplication,"Undergraduate",1);
        $this->addTrainingByType($fellowshipApplication,"Graduate",2);
        $this->addTrainingByType($fellowshipApplication,"Medical",3);
        $this->addTrainingByType($fellowshipApplication,"Residency",4);
        $this->addTrainingByType($fellowshipApplication,"Post-Residency Fellowship",5);

        $maxNumber = 1;
        $this->addTrainingByType($fellowshipApplication,"GME",6,$maxNumber);
        //$this->addTrainingByType($fellowshipApplication,"GME",6,$maxNumber);

        $maxNumber = 3;
        $this->addTrainingByType($fellowshipApplication,"Other",7,$maxNumber);
        //$this->addTrainingByType($fellowshipApplication,"Other",8,$maxNumber);
        //$this->addTrainingByType($fellowshipApplication,"Other",9,$maxNumber);

    }
    public function addTrainingByType($fellowshipApplication,$typeName,$orderinlist,$maxNumber=1) {

        $user = $fellowshipApplication->getUser();

        $specificTraining = null;

        $trainings = $user->getTrainings();

        $count = 0;

        foreach( $trainings as $training ) {
            if( $training->getTrainingType()->getName()."" == $typeName ) {
                $count++;
            }
        }

        //add up to maxNumber
        for( $count; $count < $maxNumber; $count++ ) {
            //echo "maxNumber=".$maxNumber.", count=".$count."<br>";
            $this->addSingleTraining($fellowshipApplication,$typeName,$orderinlist);
        }

    }
    public function addSingleTraining($fellowshipApplication,$typeName,$orderinlist) {

        //echo "!!!!!!!!!! add single training with type=".$typeName."<br>";

        $author = $this->sc->getToken()->getUser();
        $training = new Training($author);
        $training->setOrderinlist($orderinlist);

        $trainingType = $this->em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName($typeName);
        $training->setTrainingType($trainingType);

        //s2id_oleg_fellappbundle_fellowshipapplication_trainings_1_jobTitle
        if( $typeName == 'Other' ) {
            //otherExperience1Name => jobTitle
            //if( !$training->getJobTitle() ) {
                $jobTitleEntity = new JobTitleList();
                $training->setJobTitle($jobTitleEntity);
            //}
        }

        $fellowshipApplication->addTraining($training);
        $fellowshipApplication->getUser()->addTraining($training);

    }


    public function createApplicantListExcel( $fellappids ) {
        
        $author = $this->sc->getToken()->getUser();
        $transformer = new DateTimeToStringTransformer(null,null,'d/m/Y');
        
        $ea = new \PHPExcel(); // ea is short for Excel Application
               
        $ea->getProperties()
            ->setCreator($author."")
            ->setTitle('Fellowship Applicants')
            ->setLastModifiedBy($author."")
            ->setDescription('Fellowship Applicants list in Excel format')
            ->setSubject('PHP Excel manipulation')
            ->setKeywords('excel php office phpexcel lakers')
            ->setCategory('programming')
            ;
        
        $ews = $ea->getSheet(0);
        $ews->setTitle('Fellowship Applicants');
        
        //align all cells to left
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
        );
        $ews->getDefaultStyle()->applyFromArray($style);
        
        $ews->setCellValue('A1', 'ID'); // Sets cell 'a1' to value 'ID 
        $ews->setCellValue('B1', 'Last Name');
        $ews->setCellValue('C1', 'First Name');
        $ews->setCellValue('D1', 'Medical Degree');
        $ews->setCellValue('E1', 'Medical School');
        $ews->setCellValue('F1', 'Residency Institution');
        $ews->setCellValue('G1', 'References');
        $ews->setCellValue('H1', 'Interview Score');
        $ews->setCellValue('I1', 'Interview Date');
        
        $ews->setCellValue('J1', 'Interviewer');
        $ews->setCellValue('K1', 'Date');
        $ews->setCellValue('L1', 'Academic Rank');
        $ews->setCellValue('M1', 'Personality Rank');
        $ews->setCellValue('N1', 'Potential Rank');
        $ews->setCellValue('O1', 'Total Rank');
        $ews->setCellValue('P1', 'Language Proficiency');
        $ews->setCellValue('Q1', 'Comments');
        

        
        $row = 2;
        foreach( explode("-",$fellappids) as $fellappId ) {
        
            $fellapp = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($fellappId);
            if( !$fellapp ) {
                continue;
            }
            
            //check if author can have access to view this applicant
            //user who has the same fell type can view or edit
            if( $this->hasFellappPermission($author,$fellapp) == false ) {
                continue; //skip this applicant because the current user does not permission to view this applicant
            }
            
            $ews->setCellValue('A'.$row, $fellapp->getId());  
            $ews->setCellValue('B'.$row, $fellapp->getUser()->getLastNameUppercase());
            $ews->setCellValue('C'.$row, $fellapp->getUser()->getFirstNameUppercase());
            
            //Medical Degree
            $ews->setCellValue('D'.$row, $fellapp->getDegreeByTrainingTypeName('Medical'));
            
            //Medical School
            $ews->setCellValue('E'.$row, $fellapp->getSchoolByTrainingTypeName('Medical'));
            
            //Residency Institution
            $ews->setCellValue('F'.$row, $fellapp->getSchoolByTrainingTypeName('Residency'));
            
            //References
            $ews->setCellValue('G'.$row, $fellapp->getAllReferences());
            
            //Interview Score
            $totalScore = "";
            if( $fellapp->getInterviewScore() ) {
                $totalScore = $fellapp->getInterviewScore();
            }
            $ews->setCellValue('H'.$row, $totalScore );
	       
            //Interview Date                   
            $ews->setCellValue('I'.$row, $transformer->transform($fellapp->getInterviewDate()));
            
            $allTotalRanks = 0;
            
            foreach( $fellapp->getInterviews() as $interview ) {
            
                //Interviewer
                if( $interview->getInterviewer() ) {
                    $ews->setCellValue('J'.$row, $interview->getInterviewer()->getUsernameOptimal());
                }
                
                //Date
                $ews->setCellValue('K'.$row, $transformer->transform($interview->getInterviewDate()));
                
                //Academic Rank
                if( $interview->getAcademicRank() ) {
                    $ews->setCellValue('L'.$row, $interview->getAcademicRank()->getValue());
                }
                
                //Personality Rank
                if( $interview->getPersonalityRank() ) {
                    $ews->setCellValue('M'.$row, $interview->getPersonalityRank()->getValue());
                }
                
                //Potential Rank
                if( $interview->getPotentialRank() ) {
                    $ews->setCellValue('N'.$row, $interview->getPotentialRank()->getValue());
                }
                
                //Total Rank
                $ews->setCellValue('O'.$row, $interview->getTotalRank());
                $allTotalRanks = $allTotalRanks + $interview->getTotalRank();
                
                //Language Proficiency
                if( $interview->getLanguageProficiency() ) {
                    $ews->setCellValue('P'.$row, $interview->getLanguageProficiency()->getName());
                }
                
                //Comments
                $ews->setCellValue('Q'.$row, $interview->getComment());   
                
                $row++;
            
            } //for each interview
            
            //space in case if there is no interviewers 
            if( count($fellapp->getInterviews()) == 0 ) {
                $row++;
            }
            
            //All Total Ranks:           
            $ews->setCellValue('A'.$row, "All Total Ranks:");
            $ews->setCellValue('B'.$row, $allTotalRanks);
            
            //Avg Rank:
            $row++;
            $ews->setCellValue('A'.$row, "Avg Rank:");
            $ews->setCellValue('B'.$row, $totalScore);
            
            $row = $row + 2;
        }
        
        //exit("ids=".$fellappids);
        
        
        // Auto size columns for each worksheet
        \PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach ($ea->getWorksheetIterator() as $worksheet) {

            $ea->setActiveSheetIndex($ea->getIndex($worksheet));

            $sheet = $ea->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            /** @var PHPExcel_Cell $cell */
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }
               
        
        return $ea;
    }


    public function createInterviewApplicantList( $fellappids ) {

        $author = $this->sc->getToken()->getUser();

        $fellapps = array();

        foreach( explode("-",$fellappids) as $fellappId ) {

            $fellapp = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($fellappId);
            if( !$fellapp ) {
                continue;
            }

            //check if author can have access to view this applicant
            //user who has the same fell type can view or edit
            if( $this->hasFellappPermission($author,$fellapp) == false ) {
                continue; //skip this applicant because the current user does not permission to view this applicant
            }

            //only include the people who have an interview date (not the status of the interviewer)
            if( !$fellapp->getInterviewDate() ) {
                continue;
            }

            $fellapps[] = $fellapp;
        }

        //exit("ids=".$fellappids);
        return $fellapps;
    }



} 