<?php
/**
 * Created by PhpStorm.
 * User: oli2002
 * Date: 1/27/16
 * Time: 9:27 AM
 */

namespace Oleg\DeidentifierBundle\Security\Voter;


//use Oleg\OrderformBundle\Security\Voter\PatientHierarchyVoter;
use Oleg\UserdirectoryBundle\Security\Voter\BasePermissionVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
//use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

use Oleg\UserdirectoryBundle\Entity\User;



class DeidentifierPermissionVoter extends BasePermissionVoter //BasePermissionVoter   //PatientHierarchyVoter
{

    protected function getSiteRoleBase() {
        return 'DEIDENTIFICATOR';
    }

    protected function getSitename() {
        return 'deidentifier';  //Site abbreviation i.e. fellapp, not fellowship-applications
    }


    protected function canEdit($subject, TokenInterface $token) {
        return false;
    }

    protected function canChangeStatus($subject, TokenInterface $token) {
        return false;
    }





//NOT USED ANYMORE
//    protected function supportsLocal($attribute, $subject)
//    {
//        //return false; //testing
//        //echo "DeidentifierPermissionVoter: support <br>";
//
////        if( parent::supports($attribute, $subject) ) {
////            return true;
////        }
//
//        $attribute = $this->convertAttribute($attribute);
//
//        // if the attribute isn't one we support, return false
//        if (!in_array($attribute, array(self::CREATE, self::READ))) {
//            //exit("Not supported attribute=".$attribute."<br>");
//            return false;
//        }
//
//        if( $subject == "Accession" ) {
//            return true;
//        }
//
//        //echo "Supported subject=".$subject."<br>";
//        return false;
//    }
//
//    //$subject - object or a string "Accession"
//    protected function canViewLocal($subject, TokenInterface $token) {
//
//        //echo 'attribute='.$attribute."<br>";
//        //echo 'subject='.$subject."<br>";
//        $user = $token->getUser();
//        //return true;
//
//        if( !$user instanceof User ) {
//            return false;
//        }
//
//        //ROLE_DEIDENTIFICATOR_ADMIN can do anything
//        if( $this->decisionManager->decide($token, array('ROLE_DEIDENTIFICATOR_ADMIN')) ) {
//            //exit('admin!');
//            return true;
//        }
//
//        if( is_object($subject) ) {
//            //get object class name
//            $class = new \ReflectionClass($subject);
//            $className = $class->getShortName();
//        } else {
//            $className = $subject;
//        }
//
//        //echo "className=".$className."<br>";
//
//        //check if the user has role with a permission $subject class name (i.e. "Patient") and "read"
//        if( $this->em->getRepository('OlegUserdirectoryBundle:User')->isUserHasPermissionObjectAction( $user, $className, "read" ) ) {
//            //exit('can View! exit');
//            return true;
//        } else {
//            //echo "can not view ".$className."<br>";
//        }
//
//        //exit('no permission');
//        return false;
//
//    }
//
//
//    protected function canCreateLocal($subject, TokenInterface $token) {
//
//        //echo 'attribute='.$attribute."<br>";
//        //echo 'subject='.$subject."<br>";
//        $user = $token->getUser();
//        //return true;
//
//        if( !$user instanceof User ) {
//            return false;
//        }
//
//        //ROLE_DEIDENTIFICATOR_ADMIN can do anything
//        if( $this->decisionManager->decide($token, array('ROLE_DEIDENTIFICATOR_ADMIN')) ) {
//            //exit('admin!');
//            return true;
//        }
//
//        if( is_object($subject) ) {
//            //get object class name
//            $class = new \ReflectionClass($subject);
//            $className = $class->getShortName();
//        } else {
//            $className = $subject;
//        }
//
//        //echo "className=".$className."<br>";
//
//        //check if the user has role with a permission $subject class name (i.e. "Patient") and "read"
//        if( $this->em->getRepository('OlegUserdirectoryBundle:User')->isUserHasPermissionObjectAction( $user, $className, "create" ) ) {
//            //exit('can View! exit');
//            return true;
//        } else {
//            //echo "can not view ".$className."<br>";
//        }
//
//        //exit('no permission');
//        return false;
//
//    }




}


