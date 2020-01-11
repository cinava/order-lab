<?php
/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 1/11/2020
 * Time: 8:53 AM
 */

namespace Tests\App\TestBundle;


class CallLogKernelTest extends KernelTestBase
{

    public function testUtilMethods() {
        //$this->logIn();

        $calllogUtil = self::$container->get('calllog_util');

        $nextId = $calllogUtil->getNextEncounterGeneratedId();
        //AUTOGENERATEDENCOUNTERID-0000000000401 (Auto-generated Encounter Number)
        $this->assertStringContainsStringIgnoringCase("AUTOGENERATEDENCOUNTERID-",$nextId);

//        $resList = $calllogUtil->getPatientList();
//        $this->assertGreaterThan(0, count($resList));
//
//        $patientLists = $calllogUtil->getDefaultPatientLists();
//        $this->assertGreaterThan(0, count($patientLists));
//
//        $providers = $calllogUtil->getReferringProvidersWithUserWrappers();
//        $this->assertGreaterThan(0, count($providers));
//
//        //$msg = $calllogUtil->getTotalTimeSpentMinutes();
//        //$this->assertStringContainsStringIgnoringCase("During the current week", $msg);
//
//        $messageCategory = $calllogUtil->getDefaultMessageCategory();
//        $this->assertGreaterThan(0, $messageCategory->getId());
//
//        $keytypemrn = $calllogUtil->getDefaultMrnType();
//        $this->assertGreaterThan(0, $keytypemrn->getId());
    }

}