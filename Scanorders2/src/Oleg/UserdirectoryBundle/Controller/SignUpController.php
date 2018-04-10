<?php

namespace Oleg\UserdirectoryBundle\Controller;

use Oleg\UserdirectoryBundle\Entity\AdminComment;
use Oleg\UserdirectoryBundle\Entity\AdministrativeTitle;
use Oleg\UserdirectoryBundle\Entity\AppointmentTitle;
use Oleg\UserdirectoryBundle\Entity\BoardCertification;
use Oleg\UserdirectoryBundle\Entity\Book;
use Oleg\UserdirectoryBundle\Entity\ConfidentialComment;
use Oleg\UserdirectoryBundle\Entity\EmploymentStatus;
use Oleg\UserdirectoryBundle\Entity\Grant;
use Oleg\UserdirectoryBundle\Entity\Lecture;
use Oleg\UserdirectoryBundle\Entity\MedicalTitle;
use Oleg\UserdirectoryBundle\Entity\PrivateComment;
use Oleg\UserdirectoryBundle\Entity\Publication;
use Oleg\UserdirectoryBundle\Entity\PublicComment;
use Oleg\UserdirectoryBundle\Entity\ResearchLab;
use Oleg\UserdirectoryBundle\Entity\ResetPassword;
use Oleg\UserdirectoryBundle\Entity\SignUp;
use Oleg\UserdirectoryBundle\Entity\StateLicense;
use Oleg\UserdirectoryBundle\Entity\Training;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Form\ResetPasswordType;
use Oleg\UserdirectoryBundle\Form\UserSimpleType;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class SignUpController extends Controller
{

    protected $router;
    protected $siteName;
    protected $siteNameShowuser;
    protected $siteNameStr;
    protected $pathHome;
    protected $minimumRoles;
    protected $roleAdmins;

    public function __construct() {
        $this->siteName = 'employees'; //controller is not setup yet, so we can't use $this->container->getParameter('employees.sitename');
        $this->siteNameShowuser = 'employees';
        $this->siteNameStr = 'Employee Directory';
        $this->pathHome = 'employees_home';
        $this->minimumRoles = array('ROLE_USERDIRECTORY_OBSERVER');
        $this->roleAdmins = array('ROLE_USERDIRECTORY_ADMIN');
    }

    /**
     * Lists all signUp entities.
     *
     * @Route("/sign-up-list", name="employees_signup_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $signUps = $em->getRepository('OlegUserdirectoryBundle:SignUp')->findAll();

        return $this->render('OlegUserdirectoryBundle:SignUp:index.html.twig', array(
            'signUps' => $signUps,
            'title' => "Sign Up for ".$this->siteNameStr,
            'sitenamefull' => $this->siteNameStr,
            'sitename' => $this->siteName
        ));
    }

    /**
     * http://localhost/order/directory/sign-up/new
     * Creates a new signUp entity.
     *
     * @Route("/sign-up", name="employees_signup_new")
     * @Method({"GET", "POST"})
     */
    public function newSignUpAction(Request $request)
    {

        $userSecUtil = $this->get('user_security_utility');
        $userServiceUtil = $this->get('user_service_utility');
        $systemEmail = $userSecUtil->getSiteSettingParameter('siteEmail');
        $em = $this->getDoctrine()->getManager();
        $signUp = new Signup();
        $form = $this->createForm('Oleg\UserdirectoryBundle\Form\SignUpType', $signUp);
        $form->handleRequest($request);

        $password = $signUp->getHashPassword();
        //echo "password=$password<br>";

        if( $form->isSubmitted() ) {

            $passwordErrorCount = 0;
            if( !$password ) {
                $passwordErrorCount++;
            } else {
                //length
                if( strlen($password) < '8' || strlen($password) > '25' ) {
                    $passwordErrorCount++;
                }
                if( preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password) ) {
                    //echo 'Contains at least one letter and one number';
                } else {
                    //echo "No letter or number <br>";
                    $passwordErrorCount++;
                }
            }
            if( $passwordErrorCount > 0 ) {
                //echo "email error: $passwordErrorCount<br>";
                $passwordError = "Please make sure your password is between 8 and 25 characters and ".
                    "contains at least one letter and at least one number.";
                $form->get('hashPassword')->addError(new FormError($passwordError));
            }

            $usernameErrorCount = 0;
            if( !$signUp->getUserName() ) {
                $usernameErrorCount++;
            } else {
                if( strlen($signUp->getUserName()) < '8' || strlen($signUp->getUserName()) > '25' ) {
                    $usernameErrorCount++;
                }
            }
            if( $usernameErrorCount > 0 ) {
                $usernameError = "Please make sure your user name contains at least 8 and at most 25 characters.";
                $form->get('userName')->addError(new FormError($usernameError));
            }

            if( !$signUp->getEmail() ) {
                //$form->get('email')->addError(new FormError('The email value should not be blank.')); validation is done in DB level
            } else {
                //echo "email=".$signUp->getEmail()."<br>";
                //If the entered email address ends in “@med.cornell.edu” or “@nyp.org”
                if( strpos($signUp->getEmail(), "@med.cornell.edu") !== false || strpos($signUp->getEmail(), "@nyp.org") !== false ) {
                    $cwid = "CWID";
                    $emailArr = explode("@",$signUp->getEmail());
                    if( count($emailArr)>0 ) {
                        $cwid = $emailArr[0];
                    }
                    $emailError = "Since you entered an institutional e-mail address, you do not need to sign up for an account. ".
                                  "You can use your $cwid and the associated password to log in.";
                    $form->get('email')->addError(new FormError($emailError));
                }

                //check if still active request and email or username existed in SignUp DB
                $signUpDbs = $em->getRepository('OlegUserdirectoryBundle:SignUp')->findByEmail($signUp->getEmail());
                if( count($signUpDbs) > 0 ) {
                    $signUpDb = $signUpDbs[0];
                    $createdDate = $signUpDb->getCreatedate();
                    //echo "createDate=".$createdDate->format("d-m-Y H:i:s")."<br>";
                    $now = new \DateTime();
                    //echo "now=".$now->format("d-m-Y H:i:s")."<br>";
                    $interval = $now->diff($createdDate);
                    $hours = $interval->h + ($interval->days*24);
                    //echo "hours=".$hours."<br>";
                    if( $hours <= 48 ) {
                        //exit('registration with this email is still active');
                        $emailError = "The email address you have entered is already used and".
                            " the active registration has been sent to this email address.".
                            " Please search your email for the registration link.";
                        $form->get('email')->addError(new FormError($emailError));
                    }
                }

                //check if user with provided email existed in SignUp DB
                $userDbs = $em->getRepository('OlegUserdirectoryBundle:User')->findUserByUserInfoEmail($signUp->getEmail());
                //echo "usersDb=".count($userDbs)."<br>";
                if( count($userDbs) > 0 ) {
                    $userDb = $userDbs[0];
                    if( $userDb->getLocked() ) {
                        $emailError = "Your account is currently locked and you will not be able to log in until".
                            " you contact the system administrator at ".$systemEmail.".";
                    } else {
                        $emailError = "The email address you have entered is already taken.".
                            " Please enter a different email address above.";
                    }
                    $form->get('email')->addError(new FormError($emailError));
                }

            }

            if( $signUp->getUserName() && $usernameErrorCount == 0 ) {
                //When the user clicks “Sign Up”, search for matching existing user names
                // in the user table; if the user name is taken, show a red well stating
                // “This user name appears to be taken. Please choose another one.”
                $userDb = $em->getRepository('OlegUserdirectoryBundle:User')->findOneByPrimaryPublicUserId($signUp->getUserName());
                if( $userDb ) {
                    $form->get('userName')->addError(new FormError('This user name appears to be taken. Please choose another one.'));
                }
            }
            //exit('1');
        }//if submitted

        if( $form->isSubmitted() && $form->isValid() ) {

            //1)hash password
            //$salt = uniqid(mt_rand(), true);
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            //echo "salt=$salt<br>";
            $dummyUser = new User();
            $dummyUser->setSalt($salt);

            $encoder = $this->container->get('security.password_encoder');
            //$encoderService = $this->container->get('security.encoder_factory');
            //$encoder = $encoderService->getEncoder($dummyUser);

            $encoded = $encoder->encodePassword($dummyUser,$password);
            //echo "encoded=$encoded<br>";
            $signUp->setSalt($salt);
            $signUp->setHashPassword($encoded);
            unset($dummyUser);

            //2) Generate unique REGISTRATION-LINK-ID
            $registrationLinkId = $userServiceUtil->getUniqueRegistrationLinkId("SignUp",$signUp->getEmail());
            $signUp->setRegistrationLinkID($registrationLinkId);

            //sitename
            $siteObject = $em->getRepository('OlegUserdirectoryBundle:SiteList')->findOneByAbbreviation($this->siteName);
            if( $siteObject ) {
                $signUp->setSite($siteObject);
            }

            if( $request ) {
                $signUp->setUseragent($_SERVER['HTTP_USER_AGENT']);
                $signUp->setIp($request->getClientIp());
                $signUp->setWidth($request->get('display_width'));
                $signUp->setHeight($request->get('display_height'));
            }

            //exit('flush');
            $em->persist($signUp);
            $em->flush($signUp);

            //Email
            $newline = "\r\n";
            $emailUtil = $this->container->get('user_mailer_utility');
            $subject = $this->siteNameStr." Registration";

            //$orderUrl = ""; //[URL/order]
            $orderUrl = $this->container->get('router')->generate(
                //'main_common_home',
                $this->pathHome,
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            //$activationUrl = ""; //http://URL/order/activate-account/REGISTRATION-LINK-ID
            $activationUrl = $this->container->get('router')->generate(
                $this->siteName.'_activate_account',
                array(
                    'registrationLinkID'=>$registrationLinkId
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $body =
                "Thank You for registering at ".$orderUrl."!".
                $newline."Please visit the following link to activate your account or copy/paste it into your browser’s address bar:".
                $newline.$activationUrl.
                $newline."If you encounter any issues, please email our system administrator at $systemEmail.";
            ;

            //Event Log
            //$author = $this->get('security.token_storage')->getToken()->getUser();
            $systemuser = $userSecUtil->findSystemUser();
            $event = "New user registration has been created:<br>".$signUp;
            $event = $event . "<br>Email Subject: " . $subject;
            $event = $event . "<br>Email Body:<br>" . $body;
            $userSecUtil->createUserEditEvent($this->siteName,$event,$systemuser,$signUp,$request,'User SignUp Created');

            //                    $emails, $subject, $message, $ccs=null, $fromEmail=null
            $emailUtil->sendEmail($signUp->getEmail(), $subject, $body);

            //change status
            $signUp->setRegistrationStatus("Activation Email Sent");
            //$em->persist($signUp);
            $em->flush($signUp);

            //Confirmation
            $confirmation = "Thank You for signing up!<br>
                An email was sent to the email address you provided ".$signUp->getEmail()." with a registration link.<br>
                Please click the link emailed to you to activate your account.";
//            $this->get('session')->getFlashBag()->add(
//                'notice',
//                $confirmation
//            );

            //return $this->redirectToRoute('employees_signup_show', array('id' => $signUp->getId()));
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Registration Confirmation",
                    'messageSuccess'=>$confirmation)
            );
        }
        //exit('new');

        return $this->render('OlegUserdirectoryBundle:SignUp:new.html.twig', array(
            'signUp' => $signUp,
            'form' => $form->createView(),
            'title' => "Sign Up for ".$this->siteNameStr,
            'sitenamefull' => $this->siteNameStr,
            'sitename' => $this->siteName
        ));
    }

    /**
     * @Route("/activate-account/{registrationLinkID}", name="employees_activate_account")
     * @Method({"GET", "POST"})
     */
    public function activateAccountAction(Request $request, $registrationLinkID)
    {
        //exit('1');
        $emailUtil = $this->container->get('user_mailer_utility');
        $userServiceUtil = $this->get('user_service_utility');
        $userSecUtil = $this->get('user_security_utility');
        $em = $this->getDoctrine()->getManager();

        $signUp = $em->getRepository('OlegUserdirectoryBundle:SignUp')->findOneByRegistrationLinkID($registrationLinkID);
        if( !$signUp ) {
            $confirmation = "This activation link is invalid. Please make sure you have copied it from your email message correctly.";
//            $this->get('session')->getFlashBag()->add(
//                'notice',
//                $confirmation
//            );
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Activation Link",
                    'messageDanger'=>$confirmation
                )
            );
        }

        //If the activation link is visited more than 48 hours after the timestamp in the timestamp column,
        // show the following message on the page: “This activation link has expired. Please <sign up> again.”
        $createdDate = $signUp->getCreatedate();
        //echo "createDate=".$createdDate->format("d-m-Y H:i:s")."<br>";
        $now = new \DateTime();
        //echo "now=".$now->format("d-m-Y H:i:s")."<br>";
        $interval = $now->diff($createdDate);
        $hours = $interval->h + ($interval->days*24);
        //echo "hours=$hours <br>";
        if( $hours > 48 ) {
            $signupUrl = $this->container->get('router')->generate(
                $this->siteName."_signup_new",
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $signupUrl = ' <a href="'.$signupUrl.'">sign up</a> ';
            $confirmation = "This activation link has expired. Please ".$signupUrl." again.";
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Activation Link",
                    'messageDanger'=>$confirmation
                )
            );
        }

        //If the “Registration Status” of the Registration Link ID equals “Activated”,
        // show the following message: “This activation link has already been used. Please <log in> using your account.”
        if( $signUp->getRegistrationStatus() == "Activated" ) {
            $orderUrl = $this->container->get('router')->generate(
                $this->pathHome,
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $orderUrl = ' <a href="'.$orderUrl.'">log in</a> ';
            $confirmation = "This activation link has already been used. Please ".$orderUrl." using your account.";
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Activation Link",
                    'messageDanger'=>$confirmation
                )
            );
        }
        //exit('ok');

//        $rolesArr = array();
//        $params = array(
//            'cycle' => 'edit',
//            //'user' => $user,
//            'roles' => $rolesArr,
//            //'container' => $this->container,
//            'em' => $em
//        );
//        $form = $this->createForm('Oleg\UserdirectoryBundle\Form\SignUpConfirmationType', $signUp, array(
//            'form_custom_value'=>$params
//        ));


        //1) only if not created yet: search by $signUp->getUserName() and if $signUp->getUser() is NULL
        $user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneByPrimaryPublicUserId($signUp->getUserName());
        if( !$user && !$signUp->getUser() ) {
            ///////////// create a new user ///////////////
            $publicUserId = $signUp->getUserName();
            $username = $publicUserId . "_@_" . "local-user";

            $user = $userSecUtil->constractNewUser($username); //publicUserId_@_wcmc-cwid

            //add site specific creation string
            //$createdBy = "Manually by Translational Research WCM User";
            //$createdBy = "manual";
            $createdBy = "selfsignup-" . $this->siteName;
            $user->setCreatedby($createdBy);

            //$user->setOtherUserParam($otherUserParam);

            $user->setLocked(true);

            //add roles
            //$user->addRole('ROLE_USERDIRECTORY_OBSERVER');
            //add site minimum role
            //$user->addRole('ROLE_TRANSRES_USER');
            foreach ($this->minimumRoles as $role) {
                $user->addRole($role);
            }

            //set passwordhash
            if ($signUp->getHashPassword()) {
                $user->setPassword($signUp->getHashPassword());
            }

            //set salt
            if ($signUp->getSalt()) {
                $user->setSalt($signUp->getSalt());
            }

            //create user info
            if (count($user->getInfos()) == 0) {
                $userInfo = new UserInfo();
                $user->addInfo($userInfo);
            }

            //set user info
            $userEmail = $signUp->getEmail();
            if ($userEmail) {
                $user->setEmail($userEmail);
                //$user->setEmailCanonical($userEmail);
            }

            //set administrativeTitles
            if (count($user->getAdministrativeTitles()) == 0) {
                $user->addAdministrativeTitle(new AdministrativeTitle($user));
            }

            //set user in SignUp
            $signUp->setUser($user);

//        //Update the registration status” column in the “sign up list” table to “Activated”
//        $signUp->setRegistrationStatus("Activated");

            //delete registration link field in DB?

            //exit('flush');
            $em->persist($signUp);
            $em->persist($user);
            $em->flush();
            ///////////// EOF create a new user ///////////////

            //$user = $em->getRepository('OlegUserdirectoryBundle:User')->find($user->getId());
            //$signUp = $em->getRepository('OlegUserdirectoryBundle:SignUp')->findOneByRegistrationLinkID($registrationLinkID);

            ////////////////////// auth /////////////////////////
            // Authenticating user
            $token = new UsernamePasswordToken($user, null, 'ldap_employees_firewall', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            //For Symfony <= 2.3
            //$this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_secured_area', serialize($token));
            ////////////////////// EOF auth /////////////////////////
        }//if user is not created yet


        //$cycle = 'create';
        $cycle = 'edit';
        $rolesArr = array();
        $params = array(
            'cycle' => $cycle,
            //'cycle' => 'edit',
            'user' => $user,
            'cloneuser' => null,
            'roles' => $rolesArr,
            //'container' => null,    //$this->container,
            'em' => $em,
            'hidePrimaryPublicUserId' => true,
            'activateBtn' => true
        );
        $form = $this->createForm(UserSimpleType::class, $user, array(
            'disabled' => false,
            //'action' => $this->generateUrl( $this->container->getParameter('employees.sitename').'_create_user' ),
            //'method' => 'POST',
            'form_custom_value' => $params,
        ));

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ) {

            //Update the registration status” column in the “sign up list” table to “Activated”
            $signUp->setRegistrationStatus("Activated");

            //Set the account as “unlocked” and log in the user + send them to the “Employee Directory” homepage.
            $user->setLocked(false);

            //testing
            //foreach($user->getAdministrativeTitles() as $adminTitle) {
            //    echo "Title=".$adminTitle->getName()."; Inst=".$adminTitle->getInstitution()."<br>";
            //}

            //exit('flush');
            $em->flush();

            //Event Log
            //$author = $this->get('security.token_storage')->getToken()->getUser();
            $systemuser = $userSecUtil->findSystemUser();
            $event = "Successful Account Activation:<br>".$signUp;
            $userSecUtil = $this->get('user_security_utility');
            $userSecUtil->createUserEditEvent($this->siteName,$event,$systemuser,$signUp,$request,'Successful Account Activation');

            $this->get('session')->getFlashBag()->add(
                'notice',
                "Your account has been successfully activated."
            );

            //////////////// send email to admin //////////////////////
            $newline = "\r\n";
            $emails = $userSecUtil->getUserEmailsByRole($this->siteName,"Administrator");
            $ccEmails = $userSecUtil->getUserEmailsByRole($this->siteName,"Platform Administrator");
            $adminEmails = $userSecUtil->getUserEmailsByRole($this->siteName,null,$this->roleAdmins);
            $emails = array_merge($emails,$ccEmails);
            $emails = array_merge($emails,$adminEmails);
            $emails = array_unique($emails);
            //echo "user emails=".implode(";",$emails)."<br>";
            $subject = "New ".$signUp->getUser()." account activation for ".$this->siteNameStr;

            $userDetalsArr = $signUp->getUser()->getDetailsArr();
            $titleArr = array();
            $titleArr[] = $userDetalsArr["title"];
            $titleArr[] = $userDetalsArr["institution"];

            $body =
                "New user has been activated for the ".$this->siteNameStr.":".
                $newline.$signUp->getUser().", ".implode(", ",$titleArr);
            ;
            //                    $emails, $subject, $message, $ccs=null, $fromEmail=null
            $emailUtil->sendEmail($emails, $subject, $body); //testing
//            $this->get('session')->getFlashBag()->add(
//                'notice',
//                "subject=".$subject . "; body=" . $body
//            );
            //////////////// EOF send email to admin //////////////////////

            //send them to the “Employee Directory” homepage.
            return $this->redirectToRoute($this->pathHome);
        }
        //exit('new');

        return $this->render('OlegUserdirectoryBundle:SignUp:activation.html.twig', array(
            'signUp' => $signUp,
            'user' => $user,
            'cycle' => $cycle,
            'form' => $form->createView(),
            'title' => "Activate Account for ".$this->siteNameStr,
            'sitenamefull' => $this->siteNameStr,
            'sitename' => $this->siteName
        ));



    }

    /**
     * Finds and displays a signUp entity.
     *
     * @Route("/sign-up-show/{id}", name="employees_signup_show")
     * @Method("GET")
     */
    public function showAction(SignUp $signUp)
    {
        $deleteForm = $this->createDeleteForm($signUp);

        return $this->render('OlegUserdirectoryBundle:SignUp:new.html.twig', array(
            'signUp' => $signUp,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing signUp entity.
     *
     * @Route("/sign-up-edit/{id}", name="employees_signup_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SignUp $signUp)
    {
        $deleteForm = $this->createDeleteForm($signUp);
        $editForm = $this->createForm('Oleg\UserdirectoryBundle\Form\SignUpType', $signUp);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute($this->siteName.'_signup_edit', array('id' => $signUp->getId()));
        }

        return $this->render('OlegUserdirectoryBundle:SignUp:new.html.twig', array(
            'signUp' => $signUp,
            'title' => "Sign Up for ".$this->siteNameStr,
            'sitenamefull' => $this->siteNameStr,
            'sitename' => $this->siteName,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a signUp entity.
     *
     * @Route("/sign-up-delete/{id}", name="employees_signup_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SignUp $signUp)
    {
        $form = $this->createDeleteForm($signUp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($signUp);
            $em->flush();
        }

        return $this->redirectToRoute($this->siteName.'_signup_index');
    }

    /**
     * Creates a form to delete a signUp entity.
     *
     * @param SignUp $signUp The signUp entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SignUp $signUp)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($this->siteName.'_signup_delete', array('id' => $signUp->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }



    //create empty collections
    public function addEmptyCollections($entity,$user=null) {

        $em = $this->getDoctrine()->getManager();

        if( !$user ) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
        }

        if( count($entity->getAdministrativeTitles()) == 0 ) {
            $entity->addAdministrativeTitle(new AdministrativeTitle($user));
        }

        if( count($entity->getAppointmentTitles()) == 0 ) {
            $entity->addAppointmentTitle(new AppointmentTitle($user));
            //echo "app added, type=".$appointmentTitle->getType()."<br>";
        }

        if( count($entity->getMedicalTitles()) == 0 ) {
            $entity->addMedicalTitle(new MedicalTitle($user));
        }

        //state license
        $stateLicenses = $entity->getCredentials()->getStateLicense();
        if( count($stateLicenses) == 0 ) {
            $entity->getCredentials()->addStateLicense( new StateLicense() );
        }
        //make sure state license has attachmentContainer
        foreach( $stateLicenses as $stateLicense ) {
            $stateLicense->createAttachmentDocument();
        }

        //board certification
        $boardCertifications = $entity->getCredentials()->getBoardCertification();
        if( count($boardCertifications) == 0 ) {
            $entity->getCredentials()->addBoardCertification( new BoardCertification() );
        }
        //make sure board certification has attachmentContainer
        foreach( $boardCertifications as $boardCertification ) {
            $boardCertification->createAttachmentDocument();
        }

        if( count($entity->getEmploymentStatus()) == 0 ) {
            $entity->addEmploymentStatus(new EmploymentStatus($user));
        }
        //check if Institution is assign
        foreach( $entity->getEmploymentStatus() as $employmentStatus ) {
            $employmentStatus->createAttachmentDocument();
            //echo "employ inst=".$employmentStatus->getInstitution()."<br>";
            if( !$employmentStatus->getInstitution() ) {
                $wcmc = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("WCMC");
                if( !$wcmc ) {
                    //exit('No Institution: "WCMC"');
                    throw $this->createNotFoundException('No Institution: "WCMC"');
                }
                $mapper = array(
                    'prefix' => 'Oleg',
                    'bundleName' => 'UserdirectoryBundle',
                    'className' => 'Institution'
                );
                $pathology = $em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent(
                    "Pathology and Laboratory Medicine",
                    $wcmc,
                    $mapper
                );
                if( !$pathology ) {
                    //exit('No Institution: "Pathology and Laboratory Medicine"');
                    throw $this->createNotFoundException('No Institution: "Pathology and Laboratory Medicine"');
                }
                $employmentStatus->setInstitution($pathology);
            }
        }

        //create new comments
        if( count($entity->getPublicComments()) == 0 ) {
            $entity->addPublicComment( new PublicComment($user) );
        }
        if( $this->get('security.authorization_checker')->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_USERDIRECTORY_EDITOR') ||
            $entity->getId() && $entity->getId() == $user->getId()
        ) {
            if( count($entity->getPrivateComments()) == 0 ) {
                $entity->addPrivateComment( new PrivateComment($user) );
            }
        }
        if( $this->get('security.authorization_checker')->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            if( count($entity->getAdminComments()) == 0 ) {
                $entity->addAdminComment( new AdminComment($user) );
            }
        }
        if( $this->get('security.authorization_checker')->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            if( count($entity->getConfidentialComments()) == 0 ) {
                $entity->addConfidentialComment( new ConfidentialComment($user) );
            }
        }

        if( count($entity->getResearchLabs()) == 0 ) {
            $entity->addResearchLab(new ResearchLab($user));
        }

        if( count($entity->getGrants()) == 0 ) {
            $entity->addGrant(new Grant($user));
        }
        //check if has attachemntDocument and at least one DocumentContainers
        foreach( $entity->getGrants() as $grant ) {
            $grant->createAttachmentDocument();
        }

        if( count($entity->getTrainings()) == 0 ) {
            $entity->addTraining(new Training($user));
        }

        if( count($entity->getPublications()) == 0 ) {
            $entity->addPublication(new Publication($user));
        }

        if( count($entity->getBooks()) == 0 ) {
            $entity->addBook(new Book($user));
        }

        if( count($entity->getLectures()) == 0 ) {
            $entity->addLecture(new Lecture($user));
        }

        //Identifier EIN
//        if( count($entity->getCredentials()->getIdentifiers()) == 0 ) {
//            $entity->getCredentials()->addIdentifier( new Identifier() );
//        }

        //make sure coqAttachmentContainer, cliaAttachmentContainer exists
        $entity->getCredentials()->createAttachmentDocument();

    }












    /**
     * http://localhost/order/directory/forgot-password
     *
     * @Route("/forgot-password", name="employees_forgot_password")
     * @Method({"GET", "POST"})
     */
    public function forgotPasswordAction(Request $request)
    {
        //exit('Not Implemented Yet');
        $userSecUtil = $this->get('user_security_utility');
        $userServiceUtil = $this->get('user_service_utility');
        $systemEmail = $userSecUtil->getSiteSettingParameter('siteEmail');
        $em = $this->getDoctrine()->getManager();

        $resetPassword = new ResetPassword();
        $form = $this->createForm('Oleg\UserdirectoryBundle\Form\ResetPasswordType', $resetPassword);
        $form->handleRequest($request);

        if( $form->isSubmitted() ) {

            if( !$resetPassword->getEmail() ) {
                //$form->get('email')->addError(new FormError('The email value should not be blank.'));
            } else {
                //If the entered email address ends in “@med.cornell.edu” or “@nyp.org”
                if( strpos($resetPassword->getEmail(), "@med.cornell.edu") !== false || strpos($resetPassword->getEmail(), "@nyp.org") !== false ) {
                    $cwid = "CWID";
                    $emailArr = explode("@",$resetPassword->getEmail());
                    if( count($emailArr)>0 ) {
                        $cwid = $emailArr[0];
                    }
                    $enterpriseManagementUrl = ' <a href="https://its.weill.cornell.edu/services/accounts-and-access/password-management">enterprise password management</a> ';
                    $emailError = "The password for your $cwid can only be changed or reset by visiting the 
                        $enterpriseManagementUrl page or by calling the help desk at ‭1 (212) 746-4878‬.";
                    $form->get('email')->addError(new FormError($emailError));
                }

                //check if still active request and email or username existed in ResetPassword DB
                $resetPasswordDbs = $em->getRepository('OlegUserdirectoryBundle:ResetPassword')->findByEmail($resetPassword->getEmail());
                if( count($resetPasswordDbs) > 0 ) {
                    $resetPasswordDb = $resetPasswordDbs[0];
                    $createdDate = $resetPasswordDb->getCreatedate();
                    //echo "createDate=".$createdDate->format("d-m-Y H:i:s")."<br>";
                    $now = new \DateTime();
                    //echo "now=".$now->format("d-m-Y H:i:s")."<br>";
                    $interval = $now->diff($createdDate);
                    $hours = $interval->h + ($interval->days*24);
                    //echo "hours=".$hours."<br>";
                    if( $hours <= 48 ) {
                        //exit('registration with this email is still active');
                        $emailError = "The email address you have entered is already used and".
                            " the active password reset link has been sent to this email address.".
                            " Please search your email for the password reset link.";
                        $form->get('email')->addError(new FormError($emailError));
                    }
                }

                //check if user with provided email existed in resetPassword DB
                $userDbs = $em->getRepository('OlegUserdirectoryBundle:User')->findUserByUserInfoEmail($resetPassword->getEmail());
                //echo "usersDb=".count($userDbs)."<br>";
                if( count($userDbs) > 0 ) {
                    $userDb = $userDbs[0];
                    if( $userDb->getLocked() ) {
                        $emailError = "Your account is currently locked and you will not be able to log in until".
                            " you contact the system administrator at ".$systemEmail.".";
                        $form->get('email')->addError(new FormError($emailError));
                    }
                }
                if( count($userDbs) == 0 ) {
                    $signupUrl = $this->container->get('router')->generate(
                        $this->siteName."_signup_new",
                        array(),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                    $signupUrl = ' <a href="'.$signupUrl.'">sign up for a new account</a> ';
                    $emailError = "The email address you have entered is not associated with any active accounts.".
                        " Please $signupUrl or enter a different email address above.";
                    $form->get('email')->addError(new FormError($emailError));
                }
            }
        }//if submitted

        if( $form->isSubmitted() && $form->isValid() ) {

            //1) Generate unique REGISTRATION-LINK-ID
            $resetPasswordLinkID = $userServiceUtil->getUniqueRegistrationLinkId("ResetPassword",$resetPassword->getEmail());
            $resetPassword->setRegistrationLinkID($resetPasswordLinkID);

            //sitename
            $siteObject = $em->getRepository('OlegUserdirectoryBundle:SiteList')->findOneByAbbreviation($this->siteName);
            if( $siteObject ) {
                $resetPassword->setSite($siteObject);
            }

            if( $request ) {
                $resetPassword->setUseragent($_SERVER['HTTP_USER_AGENT']);
                $resetPassword->setIp($request->getClientIp());
                $resetPassword->setWidth($request->get('display_width'));
                $resetPassword->setHeight($request->get('display_height'));
            }

            //exit('flush');
            $em->persist($resetPassword);
            $em->flush($resetPassword);

            //Email
            $newline = "\r\n";
            $emailUtil = $this->container->get('user_mailer_utility');
            $subject = "ORDER Password reset link";

            //$activationUrl = ""; //http://URL/order/activate-account/REGISTRATION-LINK-ID
            $resetPasswordUrl = $this->container->get('router')->generate(
                $this->siteName.'_reset_password',
                array(
                    'resetPasswordLinkID'=>$resetPasswordLinkID
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $body =
                "Please visit the following link to reset your password or copy/paste it into your browser’s address bar:".
                $newline.$resetPasswordUrl.
                $newline."If you encounter any issues, please email our system administrator at $systemEmail.";
            ;

            //Event Log
            //$author = $this->get('security.token_storage')->getToken()->getUser();
            $systemuser = $userSecUtil->findSystemUser();
            $event = "ORDER Password reset link has been used for ".$this->siteNameStr." for:<br>".$resetPassword;
            $event = $event . "<br>Email Subject: " . $subject;
            $event = $event . "<br>Email Body:<br>" . $body;
            $userSecUtil->createUserEditEvent($this->siteName,$event,$systemuser,$resetPassword,$request,'Password Reset Created');

            //                    $emails, $subject, $message, $ccs=null, $fromEmail=null
            $emailUtil->sendEmail($resetPassword->getEmail(), $subject, $body);

            //change status
            $resetPassword->setRegistrationStatus("Password Reset Email Sent");
            //$em->persist($signUp);
            $em->flush($resetPassword);

            //Confirmation
            $confirmation =
                "An email was sent to the email address you provided ".$resetPassword->getEmail()." with a password reset link.<br>
                Please click the link emailed to you to reset your password.";
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Password Reset Confirmation",
                    'messageSuccess'=>$confirmation)
            );
        }
        //exit('new');

        return $this->render('OlegUserdirectoryBundle:SignUp:reset-password-request.html.twig', array(
            'resetPassword' => $resetPassword,
            'form' => $form->createView(),
            'title' => "ORDER Password Reset",
            'sitenamefull' => $this->siteNameStr,
            'sitename' => $this->siteName
        ));
    }

    /**
     * http://localhost/order/directory/reset-password
     *
     * @Route("/reset-password/{resetPasswordLinkID}", name="employees_reset_password")
     * @Method({"GET", "POST"})
     */
    public function resetPasswordAction(Request $request, $resetPasswordLinkID)
    {
        //exit("reset password by resetPasswordLinkID=".$resetPasswordLinkID);

        $emailUtil = $this->container->get('user_mailer_utility');
        //$userServiceUtil = $this->get('user_service_utility');
        $userSecUtil = $this->get('user_security_utility');
        $em = $this->getDoctrine()->getManager();

        $resetPassword = $em->getRepository('OlegUserdirectoryBundle:ResetPassword')->findOneByRegistrationLinkID($resetPasswordLinkID);
        if( !$resetPassword ) {
            $confirmation = "This reset password link is invalid. Please make sure you have copied it from your email message correctly.";
            exit($confirmation);
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Reset Password Link",
                    'messageDanger'=>$confirmation
                )
            );
        }

        //If the activation link is visited more than 48 hours after the timestamp in the timestamp column,
        // show the following message on the page: “This activation link has expired. Please <sign up> again.”
        $createdDate = $resetPassword->getCreatedate();
        //echo "createDate=".$createdDate->format("d-m-Y H:i:s")."<br>";
        $now = new \DateTime();
        //echo "now=".$now->format("d-m-Y H:i:s")."<br>";
        $interval = $now->diff($createdDate);
        $hours = $interval->h + ($interval->days*24);
        //echo "hours=$hours <br>";
        if( $hours > 48 ) {
            $resetPasswordUrl = $this->container->get('router')->generate(
                $this->siteName."_forgot_password",
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $resetPasswordUrl = ' <a href="'.$resetPasswordUrl.'">reset password</a> ';
            $confirmation = "This reset password link has expired. Please ".$resetPasswordUrl." again.";
            exit($confirmation);
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Reset Password Link",
                    'messageDanger'=>$confirmation
                )
            );
        }

        //If the “Registration Status” of the Registration Link ID equals “Reset”,
        // show the following message: “This activation link has already been used. Please <log in> using your account.”
        if( $resetPassword->getRegistrationStatus() == "Reset" ) {
            $orderUrl = $this->container->get('router')->generate(
                $this->pathHome,
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $orderUrl = ' <a href="'.$orderUrl.'">log in</a> ';
            $confirmation = "This password reset link has already been used.".
                " Please ".$orderUrl." using your new password.";
            exit($confirmation);
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Reset Password Link",
                    'messageDanger'=>$confirmation
                )
            );
        }
        //exit('ok');s

        //1) only if not created yet: search by $signUp->getUserName() and if $signUp->getUser() is NULL
        $users = $em->getRepository('OlegUserdirectoryBundle:User')->findUserByUserInfoEmail($resetPassword->getEmail());
        if( count($users) == 0 ) {
            $confirmation = "The email address you have entered is not associated with any active accounts.";
            exit($confirmation);
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Reset Password Link",
                    'messageDanger'=>$confirmation
                )
            );
        }//if user is not created yet

        $user = null;
        if( count($users) > 0 ) {
            $user = $users[0];
        } else {
            $confirmation = "Logical Error: User not found by email ".$resetPassword->getEmail();
            exit($confirmation);
            return $this->render('OlegUserdirectoryBundle:SignUp:confirmation.html.twig',
                array(
                    'title'=>"Invalid Reset Password Link",
                    'messageDanger'=>$confirmation
                )
            );
        }

        $userAuth = $this->get('security.token_storage')->getToken()->getUser();
        if( $userAuth instanceof User) {
            //already authenticated
        } else {
            //exit('before auth');
            ////////////////////// auth /////////////////////////
            $token = new UsernamePasswordToken($user, null, 'ldap_employees_firewall', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_secured_area', serialize($token));
            ////////////////////// EOF auth /////////////////////////
        }

        //$cycle = 'create';
        $cycle = 'edit';
        $params = array(
            'cycle' => $cycle,
            'user' => $user,
        );
        $form = $this->createForm(ResetPasswordType::class, $user, array(
            'disabled' => false,
            'form_custom_value' => $params,
        ));

        $form->handleRequest($request);

        $password = $user->getPassword();
        echo "password=".$password."<br>";

        if( $form->isSubmitted() ) {

            $passwordErrorCount = 0;
            if( !$password ) {
                $passwordErrorCount++;
            } else {
                //length
                if( strlen($password) < '8' || strlen($password) > '25' ) {
                    $passwordErrorCount++;
                }
                if( preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password) ) {
                    //echo 'Contains at least one letter and one number';
                } else {
                    //echo "No letter or number <br>";
                    $passwordErrorCount++;
                }
            }
            if( $passwordErrorCount > 0 ) {
                //echo "email error: $passwordErrorCount<br>";
                $passwordError = "Please make sure your password is between 8 and 25 characters and ".
                    "contains at least one letter and at least one number.";
                //$form->get('password')->addError(new FormError($passwordError));
                $form->get('password')->get('first')->addError(new FormError($passwordError));
            }

            //exit('1');
        }//if submitted


        if( $form->isSubmitted() && $form->isValid() ) {

            //Update the registration status” column in the “sign up list” table to “Reset”
            $resetPassword->setRegistrationStatus("Reset");

            //get password hash
            $encoder = $this->container->get('security.password_encoder');
            $encodedPassword = $encoder->encodePassword($user,$password);
            $user->setPassword($encodedPassword);

            //set user in SignUp
            $resetPassword->setUser($user);

            exit('reset flush');
            $em->flush($resetPassword);
            $em->flush($user);

            //Event Log
            //$author = $this->get('security.token_storage')->getToken()->getUser();
            $systemuser = $userSecUtil->findSystemUser();
            $event = "Successful Password Reset:<br>".$resetPassword;
            $userSecUtil = $this->get('user_security_utility');
            $userSecUtil->createUserEditEvent($this->siteName,$event,$systemuser,$resetPassword,$request,'Successful Password Reset');

            $this->get('session')->getFlashBag()->add(
                'notice',
                "Your password has been successfully reset."
            );

            //send them to the “Employee Directory” homepage.
            return $this->redirectToRoute($this->siteName . '_logout');
        }
        //exit('reset form');

        return $this->render('OlegUserdirectoryBundle:SignUp:reset-password-action.html.twig', array(
            'resetPassword' => $resetPassword,
            'user' => $user,
            'cycle' => $cycle,
            'form' => $form->createView(),
            'title' => "Reset Password",
            'sitenamefull' => $this->siteNameStr,
            'sitename' => $this->siteName
        ));
    }


}
