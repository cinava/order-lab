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

namespace App\OrderformBundle\Repository;

//use Doctrine\ORM\EntityRepository;

use App\UserdirectoryBundle\Repository\ListAbstractRepository;

class EducationalRepository extends ListAbstractRepository {


    public function processEntity( $message, $user ) {

        $educational = $message->getEducational();

        //echo "educational=".$educational."<br>";

        if( !$educational || $educational->isEmpty() ) {
            $message->setEducational(NULL);
            //echo "educational is empty<br>";
            //exit();
            return $message;
        }

        foreach( $educational->getUserWrappers() as $userWrapper ) {
            //echo "courseTitle=".$educational->getCourseTitle()."<br>";
            if( $educational->getCourseTitle() ) {
                $educational->getCourseTitle()->addUserWrapper($userWrapper);
            }
        }

        //exit();
        return $message;
    }

}
