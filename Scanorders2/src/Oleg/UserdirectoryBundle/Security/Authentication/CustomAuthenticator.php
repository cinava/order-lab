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
 * User: DevServer
 * Date: 3/4/15
 * Time: 12:23 PM
 */

namespace Oleg\UserdirectoryBundle\Security\Authentication;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class CustomAuthenticator implements SimpleFormAuthenticatorInterface {

    private $encoder;
    private $container;
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder,$container,$em)
    {
        $this->encoder = $encoder;
        $this->container = $container;                //Service Container
        $this->em = $em;                //Entity Manager
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        //echo "CustomAuthenticator: username=".$token->getUsername()."<br>"; //", pwd=".$token->getCredentials()
        //exit();

        $authUtil = new AuthUtil($this->container,$this->em);

        //////////////////////////////////////////////////////////////////////
        //                       1) local authentication                   //
        //////////////////////////////////////////////////////////////////////
        $user = $authUtil->LocalAuthentication($token, $userProvider);
        if( $user ) {
            return $this->getUsernamePasswordToken($user,$providerKey);
        }
        ////////////////////EOF first aperio authentication //////////////////



        //////////////////////////////////////////////////////////////////////
        //                       2) aperio authentication                   //
        //////////////////////////////////////////////////////////////////////
        $user = $authUtil->AperioAuthentication($token, $userProvider);
        if( $user ) {
            return $this->getUsernamePasswordToken($user,$providerKey);
        }
        ////////////////////EOF first aperio authentication //////////////////



        //////////////////////////////////////////////////////////////////////
        //                       3) ldap authentication                     //
        //////////////////////////////////////////////////////////////////////
        $user = $authUtil->LdapAuthentication($token, $userProvider);
        if( $user ) {
            return $this->getUsernamePasswordToken($user,$providerKey);
        }
        ////////////////////EOF first ldap authentication ////////////////////


        //////////////////////////////////////////////////////////////////////
        //                       4) External IDs                            //
        //////////////////////////////////////////////////////////////////////
        $user = $authUtil->ExternalIdAuthentication($token, $userProvider);
        if( $user ) {
            return $this->getUsernamePasswordToken($user,$providerKey);
        }
        ////////////////////EOF External IDs authentication //////////////////


        //exit('all failed');
        throw new AuthenticationException('Invalid username or password');
    }



    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
        && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }



    public function getUsernamePasswordToken($user,$providerKey) {
        return new UsernamePasswordToken(
            $user,
            NULL,   //$user->getPassword(),
            $providerKey,
            $user->getRoles()
        );
    }

} 