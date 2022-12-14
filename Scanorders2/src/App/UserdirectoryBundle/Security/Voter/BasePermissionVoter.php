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
 * Date: 12/23/15
 * Time: 11:28 AM
 */

namespace App\UserdirectoryBundle\Security\Voter;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\UserdirectoryBundle\Entity\User;


//Role have permission objects (Permission);
//Permission object has one permission (PermissionList);
//Permission has one PermissionObjectList and one PermissionActionList

//Role Voter check for the role's permission based on the object-action: isGranted("read", "Accession") or isGranted("read", $accession)

abstract class BasePermissionVoter extends Voter {

    const CREATE = 'create';
    const READ   = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete'; //mark it inactive/invalid since we don't delete; this and 3 above are for Data Quality role

    const CHANGESTATUS = 'changestatus';

    protected $decisionManager;
    protected $em;
    protected $container;

    public function __construct(AccessDecisionManagerInterface $decisionManager, $em, $container)
    {
        $this->decisionManager = $decisionManager;
        $this->em = $em;
        $this->container = $container;
    }

    //isGranted("read", "Accession") or isGranted("read", $accession)
    //$attribute: string i.e. "read"
    //$subject: string (i.e. "FellowshipApplication") or entity
    protected function supports($attribute, $subject) {
        //return false; //testing
        //exit('base: support');

        $siteRoleBase = $this->getSiteRoleBase();
        $sitename = $this->getSitename();

        $attribute = $this->convertAttribute($attribute);

        // if the attribute isn't one we support, return false
        if( !$this->supportAttribute($attribute, $subject) ) {
            return false;
        }

        //////////// check if the $subject (className string or object) is in PermissionObjectList ////////////
        //$permissionObjects = $this->em->getRepository('AppUserdirectoryBundle:User')->isUserHasPermissionObjectAction( $user, $className, "read" );
        $className = $this->getClassName($subject);

        //echo "className=".$className."<br>";
        //echo "sitename=".$sitename."<br>";

        $repository = $this->em->getRepository('AppUserdirectoryBundle:PermissionObjectList');
        $dql =  $repository->createQueryBuilder("list");
        $dql->select('list');
        $dql->leftJoin('list.sites','sites');
        $dql->where("(list.name = :objectname OR list.abbreviation = :objectname) AND (sites.name = :sitename OR sites.abbreviation = :sitename)");
        $query = $this->em->createQuery($dql);

        $query->setParameters(
            array(
                'objectname'=>$className,
                'sitename'=>$sitename
            )
        );

        $permissionObjects = $query->getResult();
        //echo "permissionObjects count=".count($permissionObjects)."<br>";

        if( count($permissionObjects) > 0 ) {
            return true;
        }
        //////////// EOF check if the $subject (className string or object) is in PermissionObjectList ////////////

        //echo "Not Supported voter: attribute=".$attribute."; subject=".$subject."<br>";
        return false;
    }
    // if the attribute isn't one we support, return false
    protected function supportAttribute($attribute, $subject) {
        $attribute = $this->convertAttribute($attribute);
        if( in_array($attribute, array(self::CREATE, self::READ, self::UPDATE, self::CHANGESTATUS)) ) {
            //exit("Not supported attribute=".$attribute."<br>");
            return true;
        }
        return false;
    }


    //if return false it redirect to main page (access_denied_url?): "You don't have permission to visit this page on Scan Order site. If you already applied for access, then try to Re-Login"
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        $attribute = $this->convertAttribute($attribute);

        $user = $token->getUser();

        if( !$user instanceof User ) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch($attribute) {

            case self::CREATE:
                return $this->canCreate($subject, $token);

            case self::READ:
                return $this->canView($subject, $token);

            case self::UPDATE:
                return $this->canEdit($subject, $token);

            case self::CHANGESTATUS:
                return $this->canChangeStatus($subject, $token);
        }

        //throw new \LogicException('This code should not be reached!');
        return false;
    }

    protected function canView($subject, TokenInterface $token)
    {
        //echo "base canView? <br>";
        //exit('base canView?');

        $user = $token->getUser();

        if( !$user instanceof User ) {
            return false;
        }

        // if they can edit, they can view
        if( $this->canEdit($subject, $token) ) {
            //echo "Base canView: user can edit <br>";
            return true;
        }

        $siteRoleBase = $this->getSiteRoleBase();
        $sitename = $this->getSitename();

        //ROLE_DEIDENTIFICATOR_ADMIN can do anything
        if( $this->decisionManager->decide($token, array('ROLE_'.$siteRoleBase.'_ADMIN')) ) {
            //exit('admin!');
            return true;
        }

        //$subject: string (i.e. "FellowshipApplication") or entity
//        if( is_object($subject) ) {
//
//            $securityUtil = $this->container->get('order_security_utility');
//            //minimum requirement: subject must be under user's permitted/collaborated institutions
//            //don't perform this check for dummy, empty objects
//            if( $subject->getId() && $subject->getInstitution() ) {
//                if( $securityUtil->isObjectUnderUserPermittedCollaboratedInstitutions($subject, $user, array("Union")) == false) {
//                    return false;
//                }
//            }
//
//        } else {
//            //if subject is string, then it must be used only to show a list of entities =>
//            //there is no institution info => skip the institution check
//        }

        //echo "Base: canView before checkPermittedInstitutions <br>";
        //minimum requirement: subject must be under user's permitted/collaborated institutions
        if( $this->checkPermittedInstitutions( $subject, $user ) == false ) {
            //exit('check Permitted Institutions: can not View exxit');
            return false;
        }

        $className = $this->getClassName($subject);

        //check if the user has role with a permission $subject class name (i.e. "Patient") and "read"
        if( $this->em->getRepository('AppUserdirectoryBundle:User')->isUserHasPermissionObjectAction( $user, $className, "read" ) ) {
            //exit('can View! exxit');
            //echo "isUserHasPermissionObjectAction!!! className=".$className."<br>";
            return true;
        } else {
            //echo "can not view ".$className."<br>";
        }

        //exit('can not View exxit');
        return false;
    }

    //$subject: string (i.e. "FellowshipApplication") or entity
    protected function canEdit($subject, TokenInterface $token)
    {
        //echo "Base canEdit? <br>";
        //echo "subject=".$subject."<br>";

        $siteRoleBase = $this->getSiteRoleBase();
        $sitename = $this->getSitename();

        $user = $token->getUser();

        if( !$user instanceof User ) {
            return false;
        }

        //dummy object just created with as new => can not edit dummy object
        if( is_object($subject) && !$subject->getId() ) {
            return false;
        }

        //ROLE_DEIDENTIFICATOR_ADMIN can do anything
        if( $this->decisionManager->decide($token, array('ROLE_'.$siteRoleBase.'_ADMIN')) ) {
            //exit('admin!');
            return true;
        }

//        //ROLE_PLATFORM_DEPUTY_ADMIN can do anything
//        if( $this->decisionManager->decide($token, array('ROLE_PLATFORM_DEPUTY_ADMIN')) ) {
//            return true;
//        }

        //minimum requirement: subject must be under user's permitted/collaborated institutions
//        //$subject: string (i.e. "FellowshipApplication") or entity
//        if( is_object($subject) ) {
//            //echo "subject is object <br>";
//            $securityUtil = $this->container->get('order_security_utility');
//
//            //don't perform this check for dummy, empty objects
//            if( $subject->getId() && $subject->getInstitution() ) {
//                if( $securityUtil->isObjectUnderUserPermittedCollaboratedInstitutions($subject, $user, array("Union")) == false ) {
//                    return false;
//                }
//            }
//        } else {
//            //if subject is string, then it must be used only to show a list of entities =>
//            //there is no institution info => skip the institution check
//            //echo "subject is string; subject=".$subject."<br>";
//        }
        //minimum requirement: subject must be under user's permitted/collaborated institutions
        if( $this->checkPermittedInstitutions( $subject, $user ) == false ) {
            return false;
        }

        //If Edit => can Read: check if the user has role with a permission $subject class name (i.e. "Patient") and "read"
        $className = $this->getClassName($subject);
        if( $this->em->getRepository('AppUserdirectoryBundle:User')->isUserHasPermissionObjectAction( $user, $className, "update" ) ) {
            //exit('can View! exxit');
            //echo "isUserHasPermissionObjectAction!!! className=".$className."<br>";
            return true;
        } else {
            //echo "can not view ".$className."<br>";
        }

        //echo "Base: can not Edit! <br>";
        return false;
    }

    protected function canCreate($subject, TokenInterface $token) {

        $siteRoleBase = $this->getSiteRoleBase();
        $sitename = $this->getSitename();

        //echo 'attribute='.$attribute."<br>";
        //echo 'can Create: subject='.$subject."<br>";
        $user = $token->getUser();
        //return true;

        if( !$user instanceof User ) {
            return false;
        }

        //ROLE_DEIDENTIFICATOR_ADMIN can do anything
        if( $this->decisionManager->decide($token, array('ROLE_'.$siteRoleBase.'_ADMIN')) ) {
            //exit('admin!');
            return true;
        }

        if( is_object($subject) ) {
            //get object class name
            $class = new \ReflectionClass($subject);
            $className = $class->getShortName();
        } else {
            $className = $subject;
        }

        //echo "className=".$className."<br>";

        //check if the user has role with a permission $subject class name (i.e. "Patient") and "create"
        if( $this->em->getRepository('AppUserdirectoryBundle:User')->isUserHasPermissionObjectAction( $user, $className, "create" ) ) {
            //exit('can View! exxit');
            return true;
        } else {
            //echo "can not update ".$className."<br>";
        }

        //exit('no permission');
        return false;
    }

    //status change: user can view and update the subject
    protected function canChangeStatus($subject, TokenInterface $token) {

        //exit("canChangeStatus: not implemented yet: overwrite in the particular permission voter");

        // if they can edit, they can view
        if( $this->canEdit($subject, $token) ) {

            //add if user has appropriate admin role: overwrite in the particular permission voter

            return true;
        }

        //exit("canChangeStatus: not implemented yet");

        return false;
    }


    //check if subject is under user's permitted/collaborated institutions
    protected function checkPermittedInstitutions( $subject, $user ) {
        //$subject: string (i.e. "FellowshipApplication") or entity
        if( is_object($subject) ) {
            //echo "subject is object <br>";
            $securityUtil = $this->container->get('order_security_utility');

            //don't perform this check for dummy, empty objects
            if( $subject->getId() && $subject->getInstitution() ) {
                if( $securityUtil->isObjectUnderUserPermittedCollaboratedInstitutions($subject, $user, array("Union")) == false ) {
                    //echo "User is not under permitted institution or collaboration <br>";
                    return false;
                }
            }
        } else {
            //if subject is string, then it must be used only to show a list of entities =>
            //there is no institution info => skip the institution check
            //echo "subject is string; subject=".$subject."<br>";
        }

        return true;
    }

    protected function isAuthor( $subject, TokenInterface $token ) {
        //echo "isAuthor: ...<br>";
        $user = $token->getUser();
        if( !$user instanceof User ) {
            return false;
        }

        if( is_object($subject) ) {
            //echo "isAuthor: compare: ".$subject->getUser()->getId() ."==". $user->getId()."<br>";
            if( $subject->getUser()->getId() == $user->getId() ) {
                return true;
            }
        } else {
            //echo "isAuthor: subject is not object<br>";
        }

        return false;
    }

    protected function getClassName($subject) {

        if( is_object($subject) ) {
            //get object class name
            $class = new \ReflectionClass($subject);
            $className = $class->getShortName();
        } else {
            $className = $subject;
        }

        return $className;
    }

    protected function convertAttribute($attribute)
    {
        switch($attribute) {

            case 'view':
            case 'show':
                return self::READ;

            case 'edit':
            case 'amend':
                return self::UPDATE;

            default:
                return $attribute;

        }

        return $attribute;
    }

}
