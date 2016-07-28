<?php

namespace Oleg\CallLogBundle\Util;
use Oleg\OrderformBundle\Entity\PatientMrn;

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 6/10/2016
 * Time: 3:04 PM
 */
class CallLogUtil
{

    protected $em;
    protected $sc;
    protected $container;

    public function __construct( $em, $sc, $container ) {
        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }


//    public function processMerge( $patientsArr ) {
//
//        foreach( $patientsArr as $patient ) {
//
//
//
//        }
//
//    }


    //auto-generating a unique MRN on Scan Order, but prepend a prefix "MERGE"
    public function autoGenerateMergeMrn( $patient ) {

        $nextKey = $this->em->getRepository('OlegOrderformBundle:Patient')->getNextNonProvided($patient,null,null,"MERGE-ID");

        //convert NOMRNPROVIDED-0000000002 to MERGE-ID-0000000002
        //$nextKey = str_replace("NOMRNPROVIDED","",$nextKey);
        //$nextKey = "MERGE-ID".$nextKey;
        //echo "nextKey=".$nextKey."<br>";
        //exit('1');

        return $nextKey;
    }

    public function addGenerateMergeMrnToPatient( $patient, $autoGeneratedMergeMrn, $provider ) {
        //$securityUtil = $this->get('order_security_utility');
        //$this->addMrn( new PatientMrn($status,$provider,$sourcesystem) );

        //Source System: ORDER Call Log Book
        $sourcesystem = $this->em->getRepository('OlegUserdirectoryBundle:SourceSystemList')->findOneByName("ORDER Call Log Book");
        if( !$sourcesystem ) {
            $msg = 'Source system not found by name ORDER Call Log Book';
            throw new \Exception($msg);
            return $msg;
        }

        $status = 'valid';
        $newMrn = new PatientMrn($status,$provider,$sourcesystem);

        //set ID
        $newMrn->setField($autoGeneratedMergeMrn);

        //set keytype MrnType "Merge ID"
        $keyTypeMergeID = $this->em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Merge ID");
        if( !$sourcesystem ) {
            $msg = 'MrnType not found by name Merge ID';
            throw new \Exception($msg);
            return $msg;
        }
        $newMrn->setKeytype($keyTypeMergeID);

        $patient->addMrn($newMrn);

        return $patient;
    }


}