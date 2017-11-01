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
 * Date: 9/27/2017
 * Time: 11:05 AM
 */

namespace Oleg\TranslationalResearchBundle\Util;


//service: transres_formnode_util

class TransResFormNodeUtil
{

    protected $container;
    protected $em;
    protected $secTokenStorage;
    protected $secAuth;

    public function __construct( $em, $container ) {
        $this->container = $container;
        $this->em = $em;
        $this->secAuth = $container->get('security.authorization_checker'); //$this->secAuth->isGranted("ROLE_USER")
        $this->secTokenStorage = $container->get('security.token_storage'); //$user = $this->secTokenStorage->getToken()->getUser();
    }


    //run: translational-research/project/generate-form-node-tree/
    public function generateTransResFormNode()
    {

        $em = $this->em;
        $formNodeUtil = $this->container->get('user_formnode_utility');
        $username = $this->container->get('security.token_storage')->getToken()->getUser();

        //root
        $categories = array(
            'All Forms' => array('HemePath Translational Research'),
        );
        $count = 20;
        $level = 0;

        $count = $formNodeUtil->addNestedsetNodeRecursevely(
            null,           //$parentCategory
            $categories,    //$categories
            $level,         //$level
            $username,      //$username
            $count          //$count
        );
        echo "addNestedsetNodeRecursevely: count=".$count."<br>";

        $parentNode = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName('HemePath Translational Research');
        echo "rootNode=".$parentNode."<br>";

        $this->createProjectFormNode($parentNode);

        return round($count);
    }

    public function createProjectFormNode($parent)
    {
        //Project fields via FormNode
        //title (text)
        //funded (boolean)
        //fundedAccountNumber (string)
        //description (text)
        //budgetSummary (text)
        //totalCost (string)
        //projectType (string)

        //irbSubmitter (User) ?
        //irbNumber (string)

        $formNodeUtil = $this->container->get('user_formnode_utility');

        $objectTypeForm = $formNodeUtil->getObjectTypeByName('Form');
        $objectTypeSection = $formNodeUtil->getObjectTypeByName('Form Section');
        $objectTypeText = $formNodeUtil->getObjectTypeByName('Form Field - Free Text');
        $objectTypeString = $formNodeUtil->getObjectTypeByName('Form Field - Free Text, Single Line');
        $objectTypeCheckbox = $formNodeUtil->getObjectTypeByName('Form Field - Checkbox');
        $objectTypeDate = $formNodeUtil->getObjectTypeByName('Form Field - Full Date');

        $objectTypeDropDownAllowNewEntries = $formNodeUtil->getObjectTypeByName('Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries');
        if( !$objectTypeDropDownAllowNewEntries ) {
            exit('object type not found by name='.'Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries');
        }

        //echo "objectTypeForm=".$objectTypeForm."<br>";

        //"Pathology Call Log Entry" [Form]
        $formParams = array(
            'parent' => $parent,
            'name' => "HemePath Translational Research Project",
            'objectType' => $objectTypeForm,
        );
        $ProjectFom = $formNodeUtil->createV2FormNode($formParams); //$formNode
        $formNodeUtil->setMessageCategoryListLink("HemePath Translational Research Project",$ProjectFom);

        //Project (Section)
        $formParams = array(
            'parent' => $ProjectFom,
            'name' => "Project",
            'objectType' => $objectTypeSection,
            'showLabel' => false,
        );
        $projectSection = $formNodeUtil->createV2FormNode($formParams);

        //title (text)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Title",
            'objectType' => $objectTypeText,
            //'showLabel' => false,
        );
        $titleText = $formNodeUtil->createV2FormNode($formParams);

        //IRB Number (string)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "IRB Number",
            'objectType' => $objectTypeString,
        );
        $titleText = $formNodeUtil->createV2FormNode($formParams);

        //Funding Source (string)
//        $formParams = array(
//            'parent' => $projectSection,
//            'name' => "IRB Funding Source",
//            'objectType' => $objectTypeString,
//        );
//        $titleText = $formNodeUtil->createV2FormNode($formParams);

        //IRB expiration date (date)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "IRB Expiration Date",
            'objectType' => $objectTypeDate,
        );
        $titleText = $formNodeUtil->createV2FormNode($formParams);

        //projectType (string)
//        $formParams = array(
//            'parent' => $projectSection,
//            'name' => "Project Type",
//            'objectType' => $objectTypeString,
//        );
//        $newField = $formNodeUtil->createV2FormNode($formParams);
        //projectType - ProjectTypeList ('Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries')
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Project Type",
            'objectType' => $objectTypeDropDownAllowNewEntries,
            'classNamespace' => "Oleg\\TranslationalResearchBundle\\Entity",
            'className' => "ProjectTypeList"
        );
        $formNodeUtil->createV2FormNode($formParams);

        //funded (boolean)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Funded",
            'objectType' => $objectTypeCheckbox,
        );
        $newField = $formNodeUtil->createV2FormNode($formParams);

        //fundedAccountNumber (string)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "If funded, please provide account number",
            'objectType' => $objectTypeString,
        );
        $newField = $formNodeUtil->createV2FormNode($formParams);

        //description (text)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Brief Description",
            'objectType' => $objectTypeText,
        );
        $newField = $formNodeUtil->createV2FormNode($formParams);

        //budgetSummary (text)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Provide a Detailed Budget Outline/Summary",
            'objectType' => $objectTypeText,
        );
        $newField = $formNodeUtil->createV2FormNode($formParams);

        //totalCost (string)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Estimated Total Costs ($)",
            'objectType' => $objectTypeString,
        );
        $newField = $formNodeUtil->createV2FormNode($formParams);



    }


    /////////////////////// Request form ////////////////////////
    //run: translational-research/request/generate-form-node-tree/
    public function generateTransResFormNodeRequest()
    {

        $em = $this->em;
        $formNodeUtil = $this->container->get('user_formnode_utility');
        $username = $this->container->get('security.token_storage')->getToken()->getUser();

        //root
        $categories = array(
            'All Forms' => array('HemePath Translational Research'),
        );
        $count = 20;
        $level = 0;

        $count = $formNodeUtil->addNestedsetNodeRecursevely(
            null,           //$parentCategory
            $categories,    //$categories
            $level,         //$level
            $username,      //$username
            $count          //$count
        );
        echo "addNestedsetNodeRecursevely: count=".$count."<br>";

        $parentNode = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName('HemePath Translational Research');
        echo "rootNode=".$parentNode."<br>";

        $this->createRequestFormNode($parentNode);

        return round($count);
    }

    public function createRequestFormNode($parent)
    {
        //Request fields via FormNode
        //Requested #
        //category (fees)
        //Completed #
        //Comment

        $formNodeUtil = $this->container->get('user_formnode_utility');

        $objectTypeForm = $formNodeUtil->getObjectTypeByName('Form');
        $objectTypeSection = $formNodeUtil->getObjectTypeByName('Form Section');
        $objectTypeString = $formNodeUtil->getObjectTypeByName('Form Field - Free Text, Single Line');
        $objectTypeText = $formNodeUtil->getObjectTypeByName('Form Field - Free Text');

        $objectTypeDropDown = $formNodeUtil->getObjectTypeByName('Form Field - Dropdown Menu');
        if( !$objectTypeDropDown ) {
            exit('object type not found by name='.'Form Field - Dropdown Menu');
        }

        //echo "objectTypeForm=".$objectTypeForm."<br>";
        //"Pathology Call Log Entry" [Form]
        $formParams = array(
            'parent' => $parent,
            'name' => "HemePath Translational Research Request",
            'objectType' => $objectTypeForm,
        );
        $RequestFom = $formNodeUtil->createV2FormNode($formParams); //$formNode
        $formNodeUtil->setMessageCategoryListLink("HemePath Translational Research Request",$RequestFom);

        //Request (Section)
        $formParams = array(
            'parent' => $RequestFom,
            'name' => "Request",
            'objectType' => $objectTypeSection,
            'showLabel' => false,
        );
        $projectSection = $formNodeUtil->createV2FormNode($formParams);

        //Requested # (string)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Requested #",
            'objectType' => $objectTypeString,
        );
        $titleText = $formNodeUtil->createV2FormNode($formParams);

        //category (fees) - RequestCategoryTypeList ('Form Field - Dropdown Menu')
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Category Type",
            'objectType' => $objectTypeDropDown,
            'classNamespace' => "Oleg\\TranslationalResearchBundle\\Entity",
            'className' => "RequestCategoryTypeList"
        );
        $formNodeUtil->createV2FormNode($formParams);

        //Completed # (string)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Completed #",
            'objectType' => $objectTypeString,
        );
        $titleText = $formNodeUtil->createV2FormNode($formParams);

        //Comment (text)
        $formParams = array(
            'parent' => $projectSection,
            'name' => "Comment",
            'objectType' => $objectTypeText,
        );
        $newField = $formNodeUtil->createV2FormNode($formParams);

    }
    /////////////////////////////////////////////////////////////


    public function getProjectFormNodeFieldByName(
        $entity,
        $fieldName,
        $parentNameStr = "HemePath Translational Research",
        $formNameStr = "HemePath Translational Research Project",
        $entityFormNodeSectionStr = "Project",
        $asEntity=false
    ) {

        $formNodeUtil = $this->container->get('user_formnode_utility');

        $value = null;
        $receivingEntity = null;

        $mapper = array(
            'prefix' => "Oleg",
            'className' => "FormNode",
            'bundleName' => "UserdirectoryBundle"
        );

        //1) get FormNode by fieldName
        //$parentNameStr = "HemePath Translational Research"; //must be unique name
        $parentNode = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName($parentNameStr);
        if( !$parentNode ) {
            throw new \Exception( "FormNode parent not found by '".$parentNameStr."'" );
        }

        //$formNameStr = "HemePath Translational Research Project"; //Project's form
        $entityFormNode = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent($formNameStr,$parentNode,$mapper);
        if( !$entityFormNode ) {
            throw new \Exception( "FormNode project's form not found by '".$formNameStr."'" );
        }

        //$projectFormNodeSectionStr = "Project"; //Project's form section
        $entityFormNodeSection = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent($entityFormNodeSectionStr,$entityFormNode,$mapper);
        if( !$entityFormNodeSection ) {
            throw new \Exception( "FormNode project's form section not found by '".$entityFormNodeSectionStr."'" );
        }

        $fieldFormNode = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent($fieldName,$entityFormNodeSection,$mapper);
        if( !$fieldFormNode ) {
            throw new \Exception( "FormNode field form not found by '".$fieldName."'" );
        }

        //2) get field for this particular project

        $class = new \ReflectionClass($entity);
        $className = $class->getShortName();
        $classNamespace = $class->getNamespaceName();
        $entityMapper = array(
            'entityNamespace' => $classNamespace,   //"Oleg\\TranslationalResearchBundle\\Entity",
            'entityName' => $className, //"Project",
            'entityId' => $entity->getId(),
        );

        $complexRes = $formNodeUtil->getFormNodeValueByFormnodeAndReceivingmapper($fieldFormNode,$entityMapper);
        if( $complexRes ) {
            $formNodeValue = $complexRes['formNodeValue'];
            $receivingEntity = $complexRes['receivingEntity'];
            //echo $fieldName.": getProjectFormNodeFieldByName formNodeValue=".$formNodeValue."<br>";
            //echo $fieldName.": getProjectFormNodeFieldByName receivingEntity=".$receivingEntity->getId()."<br>";

            //process userWrapper case
            $value = $formNodeUtil->processFormNodeValue($fieldFormNode,$receivingEntity,$formNodeValue);
            //echo $fieldName.": getProjectFormNodeFieldByName value=".$value."<br>";
        }

        if( $asEntity && $receivingEntity ) {
            //$listElement = $formNodeUtil->getUniqueFormNodeListRecord($receivingEntity->getFormNode(),$entity);
            //echo "listElement ID=".$listElement->getId()."<br>";
            //TODO: find dropdown list entity
            $formNode = $receivingEntity->getFormNode();
            //echo "find dropdown list entity  formNodeID=".$formNode->getId()."<br>";
            //echo "getObjectTypeName=".$formNode->getObjectTypeName()."<br>";
            if( $formNode ) {
                if (
                    $formNode->getObjectTypeName() == "Form Field - Dropdown Menu - Allow Multiple Selections" ||
                    $formNode->getObjectTypeName() == "Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries" ||
                    $formNode->getObjectTypeName() == "Form Field - Dropdown Menu - Allow New Entries" ||
                    $formNode->getObjectTypeName() == "Form Field - Dropdown Menu"
                ) {
                    $dropdownObject = $formNodeUtil->getReceivingObject($formNode, $value);
                    //echo "dropdownObject ID=".$dropdownObject->getId()."<br>";
                    //echo "dropdownObject getSection=".$dropdownObject->getSection()."<br>";
                    //echo "dropdownObject getFeeUnit=".$dropdownObject->getFeeUnit()."<br>";
                    //echo "dropdownObject getFee=".$dropdownObject->getFee()."<br>";

                    return $dropdownObject;
                }
            }
        }

        return $value;
    }

}