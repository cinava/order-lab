<?php
/**
 * Created by PhpStorm.
 * User: oli2002
 * Date: 1/27/16
 * Time: 9:27 AM
 */

namespace Oleg\VacReqBundle\Security\Voter;


use Oleg\UserdirectoryBundle\Security\Voter\BasePermissionVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;




class VacReqPermissionVoter extends BasePermissionVoter //BasePermissionVoter   //PatientHierarchyVoter
{

    protected function getSiteRoleBase() {
        return 'VACREQ';
    }

    protected function getSitename() {
        return 'vacreq';  //Site abbreviation i.e. fellapp, not fellowship-applications
    }


//    protected function canEdit($subject, TokenInterface $token) {
//        return false;
//    }
//
//    protected function canChangeStatus($subject, TokenInterface $token) {
//        return false;
//    }

}


