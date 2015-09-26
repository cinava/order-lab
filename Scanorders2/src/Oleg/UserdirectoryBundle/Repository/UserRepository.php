<?php


namespace Oleg\UserdirectoryBundle\Repository;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository {


    public function findAllByInstitutionNodeAsUserArray( $nodeid ) {

        $users = $this->findAllByInstitutionNode($nodeid);
        $output = $this->convertUsersToArray($users);

        return $output;
    }

    public function findAllByInstitutionNode( $nodeid ) {

        $query = $this->_em->createQueryBuilder()
            ->from('OlegUserdirectoryBundle:User', 'user')
            ->select("user")
            ->groupBy('user');


        $query->orderBy("user.primaryPublicUserId","ASC");
        $query->leftJoin("user.administrativeTitles", "administrativeTitles");
        $query->leftJoin("user.appointmentTitles", "appointmentTitles");
        $query->leftJoin("user.medicalTitles", "medicalTitles");
        $query->where("administrativeTitles.institution = :nodeid OR appointmentTitles.institution = :nodeid OR medicalTitles.institution = :nodeid");
        $query->setParameters( array("nodeid"=>$nodeid) );

        $users = $query->getQuery()->getResult();

        return $users;
    }


    public function convertUsersToArray( $users ) {

        $output = array();
        foreach( $users as $user ) {

            $userStr = $user->getUsernameShortest();

            $phoneArr = array();
            foreach( $user->getAllPhones() as $phone ) {
                $phoneArr[] = $phone['prefix'] . $phone['phone'];
            }
            if( count($phoneArr) > 0 ) {
                $userStr = $userStr . " " . implode(", ", $phoneArr);
            }

            $emailArr = array();
            foreach( $user->getAllEmail() as $email ) {
                $emailArr[] = $email['prefix'] . $email['email'];
            }
            if( count($emailArr) > 0 ) {
                $userStr = $userStr . " " . implode(", ", $emailArr);
            }

            $element = array(
                'id' => 'addnodeid-'.$user->getId(),
                'addnodeid' => $user->getId(),
                'text' => $userStr,         //$user."",
                'type' => 'iconUser',
            );
            $output[] = $element;

        }//foreach

        return $output;
    }


    //Castro Martinez, Mario A: lastName, firstName
    public function findOneByNameStr( $nameStr, $orAnd="OR" ) {

        $user = null;

        $nameStrArr = explode(",",$nameStr);

        $lastName = trim($nameStrArr[0]);
        $firstName = trim($nameStrArr[1]);

        $query = $this->_em->createQueryBuilder()
            ->from('OlegUserdirectoryBundle:User', 'user')
            ->select("user");

        $query->leftJoin("user.infos", "infos");

        $query->where("infos.firstName = :firstName ".$orAnd." infos.lastName = :lastName");
        $query->setParameters( array("firstName"=>$firstName, "lastName"=>$lastName) );

        $users = $query->getQuery()->getResult();

        if( count($users) > 0 ) {
            $user = $users[0];
        }

        return $user;
    }



    public function findOneUserByRole($role) {

        $user = null;

        $users = $this->findUserByRole($role);

        if( count($users) > 0 ) {
            $user = $users[0];
        }

        return $user;
    }

    public function findUserByRole($role) {

        $user = null;

        $query = $this->_em->createQueryBuilder()
            ->from('OlegUserdirectoryBundle:User', 'list')
            ->select("list")
            ->where("list.roles LIKE :role")
            ->orderBy("list.id","ASC")
            ->setParameter('role', '%"' . $role . '"%');

        return $query->getQuery()->getResult();
    }

}

