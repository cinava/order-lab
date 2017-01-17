<?php
namespace Oleg\UserdirectoryBundle\Util;

use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\SingleUserWrapperTransformer;
use Symfony\Component\HttpFoundation\Response;
use Oleg\UserdirectoryBundle\Entity\FormNode;
use Oleg\UserdirectoryBundle\Entity\ObjectTypeText;


/**
 * Description of FormNodeUtil
 *
 * @author Cina
 */
class FormNodeUtil
{

    protected $em;
    protected $sc;
    protected $container;

    public function __construct($em, $sc, $container)
    {
        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }

    public function processFormNodes($request, $formNodeHolder, $holderEntity, $testing=false)
    {
        if( !$formNodeHolder ) {
            return;
        }

        $formNodes = $formNodeHolder->getFormNodes();
        if( !$formNodes ) {
            return;
        }

//        $rootFormNode = $formNode->getRootName($formNode);
//        if( !$rootFormNode ) {
//            exit("No Root of the node ".$formNode."<br>");
//            return;
//        }

        $data = $request->request->all();

//        print "<pre>";
//        print_r($data);
//        print "</pre>";

        //$unmappedField = $data["formnode-4"];
        //echo "<br>unmappedField=" . $unmappedField . "<br>";
        //$unmappedField = $data["formnode-6"];
        //echo "<br>unmappedField=" . $unmappedField . "<br>";
        //echo "<br><br>";

        //process by form root's children nodes
        //$this->processFormNodeRecursively($data,$rootFormNode,$holderEntity);

        //process by data partial key name" "formnode-4" => "formnode-"
        $this->processFormNodesFromDataKeys($data,$holderEntity,$testing);
    }

    //process by data partial key name" "formnode-4" => "formnode-"
    public function processFormNodesFromDataKeys($data,$holderEntity,$testing=false) {
        if( !array_key_exists('formnode', $data) ) {
            //exit('no formnode data exists');
            return;
        }
        $formnodeData = $data['formnode'];

        foreach( $formnodeData as $formNodeId => $formValue ) {
            //if( strpos($key, 'formnode-') !== false ) {
                //$formNodeId = str_replace('formnode-','',$key);
                //$keyArr = explode("-",$key);
                //id is second element
                //$formNodeId = $keyArr[1];
                if( $testing ) {
                    echo "<br>############ formNodeId=" . $formNodeId . ": " . $formValue . " ############<br>";
                }
                // do whatever you need to with $formNodeId...
                $thisFormNode = $this->em->getRepository("OlegUserdirectoryBundle:FormNode")->find($formNodeId);
                if( !$thisFormNode ) {
                    //exit("No Root of the node id=".$formNodeId."<br>");
                    continue;
                }
                $this->processFormNodeByType($thisFormNode,$formValue,$holderEntity,$testing);
            //}
        }
    }

    //NOT USED
//    public function processFormNodeRecursively( $data, $formNode, $holderEntity ) {
//
//        echo "formNode=".$formNode."<br>";
//        $children = $formNode->getChildren();
//        if( $children ) {
//
//            foreach( $children as $childFormNode ) {
//                $this->processFormNodeByType($data,$childFormNode,$holderEntity);
//                $this->processFormNodeRecursively($data,$childFormNode,$holderEntity);
//            }
//
//        } else {
//            $this->processFormNodeByType($data,$formNode,$holderEntity);
//        }
//
//    }

    public function processFormNodeByType( $formNode, $formValue, $holderEntity, $testing=false ) {

        $formNodeObjectName = $formNode->getObjectTypeName();
//        if( $formNode->getObjectType() ) {
//            $formNodeObjectName = $formNode->getObjectType()->getName()."";
//        }

        if( !$this->hasValue($formNode) && $formNodeObjectName != "Form Section Array" ) {
            //exit("No Value of the node=".$formNode."<br>");
            return;
        }

        //$key = $formNode->getId();
        //$formValue = $data['formnode'][$key];
        //echo $formNode->getId().": formValue=" . $formValue . "<br>";

        if( !$formValue ) {
            //exit("No Value=".$formValue."<br>");
            return;
        }
        //exit("Value=[".$formValue."]<br>");

//        //"Allow Multiple Selections"
//        if( $formNodeObjectName == "Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries" ) {
//            //$formValue is an array: newvalue1,newvalue2,newvalue3
//            $formValueArr = explode(",",$formValue);
//            foreach( $formValueArr as $thisFormValue ) {
//                $newListElement = $this->createFormNodeListRecord($formNode,$thisFormValue,$holderEntity,$testing);
//
//                if( !$testing ) {
//                    $this->em->persist($newListElement);
//                    $this->em->flush($newListElement);
//                }
//            }
//
//            return;
//        }
//
//        //exception: time [hour] [minute]
//        if( $formNodeObjectName == "Form Field - Time" ) {
//            //$formValue is an array: Array ( [time] => Array ( [hour] => 11 [minute] => 51 ) )
//            //use ObjectTypeDateTime's timeValue
//
//            $formValueHour = $formValue['time']['hour'];
//            $formValueMinute = $formValue['time']['minute'];
//            $formValueStr = $formValueHour.":".$formValueMinute;
//
//            $newListElement = $this->createFormNodeListRecord($formNode,$formValueStr,$holderEntity,$testing);
//
//            $newListElement->setTimeValueHourMinute($formValueHour,$formValueMinute);
//
//            if( !$testing ) {
//                $this->em->persist($newListElement);
//                $this->em->flush($newListElement);
//            }
//
//            return;
//        }

        //All others
        if( is_array($formValue) ) {
            //$formValue is array
            //echo $formNodeObjectName.": formValue is array:";
//            print "<pre>";
//            print_r($formValue);
//            print "</pre>";
//            echo "<br>";

            if( array_key_exists('arraysectioncount', $formValue) ) {
                //echo $formNode.": ".$formNodeObjectName.": formValue is arraysectioncount <br>";
                //record section array index including parent index: 0-0, 0-1 (array section 1 (index 0) includes two array sections (indexes 0 and 1))
                $this->createArraysectionListRecord($formNode, $formValue, $holderEntity, $testing);
            } else {
                //echo $formNodeObjectName.": formValue is regular array <br>";
                foreach( $formValue as $thisFormValue ) {
                    $this->createFormNodeListRecord($formNode, $thisFormValue, $holderEntity, $testing);
                }
            }
        } else {
            //echo $formNodeObjectName.": formValue is single formValue=" . $formValue . "<br>";
            $this->createFormNodeListRecord($formNode,$formValue,$holderEntity,$testing);
        }

    }
    public function createArraysectionListRecord( $formNode, $formValue, $holderEntity, $testing=false, $params=null ) {
        foreach( $formValue['arraysectioncount'] as $arraysectioncount=>$thisFormValue ) {

//            echo $formNode->getId() . ": arraysectioncount=" . $arraysectioncount . ":<br>";
//            print "<pre>";
//            print_r($thisFormValue);
//            print "</pre><br>";

            foreach( $thisFormValue['node'] as $sectionFormnodeId => $thisThisFormValue ) {
                //clean $arraysectioncount: fffsa_0-0_fffsa => 0-0
                $arraysectioncount = $this->getCleanedArraySection($arraysectioncount);
                $params = array(
                    'arraySectionIndex' => $arraysectioncount,
                    'arraySectionId' => $formNode->getId(),
                );
                $sectionFormnode = $this->em->getRepository("OlegUserdirectoryBundle:FormNode")->find($sectionFormnodeId);
                $this->createFormNodeListRecord($sectionFormnode, $thisThisFormValue, $holderEntity, $testing, $params);
            }
        }
    }

    public function createFormNodeListRecord( $formNode, $formValue, $holderEntity, $testing=false, $params=null ) {

        $formNodeObjectName = $formNode->getObjectTypeName();

        if( $testing ) {
            echo $formNode->getId().": formNodeObjectName:".$formNodeObjectName."<br>";
        }

        $newListElement = null;

        //"Allow Multiple Selections"
        if(
            $formNodeObjectName == "Form Field - Dropdown Menu - Allow Multiple Selections" ||
            $formNodeObjectName == "Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries" ||
            $formNodeObjectName == "Form Field - Dropdown Menu - Allow New Entries"
        ) {

            if( !$formValue ) {
                return;
            }

//            echo "formNodeObjectName:".$formNodeObjectName."; formValue=".$formValue."<br>";
//            print "@@@@@@@@@@@@@@@@@ <pre>";
//            print_r($formValue);
//            print "</pre><br>";

            $noflush = true; //don't flush because setValues must be set after
            $newListElement = $this->createSingleFormNodeListRecord($formNode,$formValue,$holderEntity,$noflush,$params);

            //$formValue is an array: newvalue1,newvalue2,newvalue3
            if( is_array($formValue) ) {
                $formValueArr = $formValue;
            } else {
                $formValueArr = explode(",",$formValue);
            }
            if( count($formValueArr) > 0 ) {
                $newListElement->setIdValues($formValueArr);
            }

            if( !$testing ) {
                $this->em->persist($newListElement);
                $this->em->flush($newListElement);
            }

        }

        if(
            $formNodeObjectName == "Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries" ||
            $formNodeObjectName == "Form Field - Dropdown Menu - Allow New Entries"
        ) {
            //find if list already exists

            $className = $formNode->getEntityName();
            $classNamespace = $formNode->getEntityNamespace();

            if( $className && $classNamespace ) {

                //$classNamespace: Oleg\UserdirectoryBundle\Entity => UserdirectoryBundle
                $bundleNameArr = explode("\\",$classNamespace);
                $bundleName = null;
                if( count($bundleNameArr) > 2 ) {
                    $bundleName = $bundleNameArr[1];
                }

                if( $bundleName ) {

                    $creator = $this->sc->getToken()->getUser();

                    $transformer = new GenericTreeTransformer($this->em, $creator, $className, $bundleName);

                    $userWrapperTransformer = null;
                    if( $className == "PathologyResultSignatoriesList" ) {
                        $userWrapperTransformer = new SingleUserWrapperTransformer($this->em, $this->container, $creator, 'UserWrapper');
                    }

                    //$formValue is an array: newvalue1,newvalue2,newvalue3
                    $formValueArr = explode(",", $formValue);
                    foreach( $formValueArr as $thisValue ) {
                        //echo "<br>----- ### ".$className.": thisValue=" . $thisValue . " ###<br>";
                        $dropdownObject = $transformer->reverseTransform($thisValue);

                        if( $dropdownObject ) {
                            $class = new \ReflectionClass($dropdownObject);
                            $thisclassName = $class->getShortName();
                            //echo "=======> ".$thisclassName.": Adding dropdownObject=" . $dropdownObject . "; id=". $dropdownObject->getId() . "<br>";

//                            if( $className == "PathologyResultSignatoriesList" ) {
//                                $userWrapper = $dropdownObject->getUserWrapper();
//                                echo "userWrapper: name=" . $userWrapper->getName() . "; user=" . $userWrapper->getUser() . "<br>";
//                            }

                            if( $className == "PathologyResultSignatoriesList" && $userWrapperTransformer ) {
                                //for PathologyResultSignatoriesList user id is $dropdownObject's entityId
                                $userId = $dropdownObject->getEntityId();
                                //echo "get userWrapper by user id=".$userId."<br>";
                                if( $userId ) {
                                    $userWrapper = $userWrapperTransformer->reverseTransformByType($userId,'User');
                                } else {
                                    $userWrapper = $userWrapperTransformer->reverseTransform($thisValue);
                                }
                                //echo "userWrapper: name=" . $userWrapper->getName() . "; user=" . $userWrapper->getUser() . "<br>";
                                $dropdownObject->setUserWrapper($userWrapper);

                                //set object type as User
                                if( $userWrapper->getUser() ) {
                                    $dropdownObject->setObject($userWrapper->getUser());
                                }

                            }

                            if (!$testing) {
                                //$this->em->persist($dropdownObject);
                                $this->em->flush();
                            }
                        }
                    }

                }//if bundleName

            }

        }

        if( $newListElement ) {
            return;
        }

        //exception: time [date] [hour] [minute]
        if(
            $formNodeObjectName == "Form Field - Time" ||
            $formNodeObjectName == "Form Field - Time, with Time Zone" ||
            $formNodeObjectName == "Form Field - Full Date and Time" ||
            $formNodeObjectName == "Form Field - Full Date and Time, with Time Zone"
        ) {
            //$formValue is an array: Array ( [time] => Array ( [hour] => 11 [minute] => 51 ) )
            //use ObjectTypeDateTime's timeValue

//            print "Date/Time <pre>";
//            print_r($formValue);
//            print "</pre><br>";

            $formValueStr = "";

            if( $formValue && array_key_exists('time', $formValue) ) {
                $formValue = $formValue['time'];
            }

            $formValueHour = $formValue['hour'];
            $formValueMinute = $formValue['minute'];

            if( $formValueHour || $formValueHour ) {
                $formValueStr = $formValueHour . ":" . $formValueMinute;
            }

            $formValueDate = null;
            if( $formValue && array_key_exists('date', $formValue) ) {
                $formValueDate = $formValue['date'];
                if( $formValueDate ) {
                    $formValueStr = $formValueDate . " " . $formValueStr;
                }
            }

            $formValueTimezone = null;
            if( $formValue && array_key_exists('timezone', $formValue) ) {
                $formValueTimezone = $formValue['timezone'];
                if( $formValueTimezone ) {
                    $formValueStr = $formValueStr . " " . $formValueTimezone;
                }
            }

            //echo "0 datetime: date=$formValueDate hour=$formValueHour minute=$formValueMinute<br>";
            if( !$formValueDate && !$formValueHour && !$formValueMinute ) {
                return;
            }
            //echo $formNode->getId().": datetime: formValueStr=$formValueStr<br>";

            $newListElement = $this->createSingleFormNodeListRecord($formNode,$formValueStr,$holderEntity,$testing,$params);

            //$newListElement->setTimeValueHourMinute($formValueHour,$formValueMinute);
            $newListElement->setDateTimeValueDateHourMinute($formValueTimezone,$formValueDate,$formValueHour,$formValueMinute);

            if( $testing ) {
                echo $formNode->getId().": $formNodeObjectName, formValueStr=$formValueStr; ";
                echo $newListElement->getDatetimeValue()->format('m/d/Y H:i:s');
            }

            if( !$testing ) {
                $this->em->persist($newListElement);
                $this->em->flush($newListElement);
            }

            return;
        }

        //exception: time [date] [hour] [minute]
        if(
            $formNodeObjectName == "Form Field - Checkboxes"
        ) {
            $formValueArr = array();
            foreach( $formValue as $dropdownId => $thisValue ) {
                //echo "checkbox: dropdownId=".$dropdownId."; value=".$thisValue."<br>";
                $formValueArr[] = $dropdownId;
            }
            $formValueStr = implode(", ",$formValueArr);
            $noflush = true; //don't flush because setValues must be set after
            $newListElement = $this->createSingleFormNodeListRecord($formNode,$formValueStr,$holderEntity,$noflush);

            if( count($formValueArr) > 0 ) {
                $newListElement->setIdValues($formValueArr);
            }

            if( !$testing ) {
                $this->em->persist($newListElement);
                $this->em->flush($newListElement);
            }

            return;
        }

        //all other cases
        $this->createSingleFormNodeListRecord($formNode, $formValue, $holderEntity, $testing, $params);
    }
    public function createSingleFormNodeListRecord( $formNode, $formValue, $holderEntity, $noflush=false, $params=null ) {

//        echo "formnode-".$formNode->getId().": formValue=" . $formValue ."<br>";
//        if( $params ) {
//            echo "params:<br>";
//            echo "arraySectionIndex=".$params['arraySectionIndex']."; arraySectionId=" . $params['arraySectionId'] ."<br>";
//            //print "<pre>";
//            //print_r($params);
//            //print "</pre><br>";
//        }

        //1) create a new list element
        $newListElement = $this->createNewList($formNode);
        //echo "newListElement=".$newListElement."<br>";
        if( !$newListElement ) {
            //exit("No newListElement created: formNode=".$formNode."; formValue=".$formValue."<br>");
            return null;
        }

        //2) add value to the created list
        if( $formValue ) {
            $newListElement->setValue($formValue);
        }

        //3) set message by entityName to the created list
        $newListElement->setObject($holderEntity);

        //4) set formnode to the list
        $newListElement->setFormNode($formNode);

        //set additional parameters
        if( $params ) {
            //echo "params: arraySectionIndex=".$params['arraySectionIndex']."; arraySectionId=" . $params['arraySectionId'] ."<br>";
            if( array_key_exists('arraySectionIndex', $params) ) {
                $newListElement->setArraySectionIndex($params['arraySectionIndex']);
            }
            if( array_key_exists('arraySectionId', $params) ) {
                $newListElement->setArraySectionId($params['arraySectionId']);
            }
            //echo "sectionIndex=".$newListElement->getArraySectionIndex()."<br>";
            //if( array_key_exists('dropdownValues', $params) ) {
            //    $newListElement->setValues($params['dropdownValues']);
            //}
        }

        //testing
        if( 0 ) {
            $class = new \ReflectionClass($newListElement);
            $className = $class->getShortName();
            $classNamespace = $class->getNamespaceName();
            echo "newListElement list: classNamespace=" . $classNamespace . ", className=" . $className . ", Value=" . $newListElement->getValue() . "<br>";
            //echo "newListElement list: Namespace=" . $newListElement->getEntityNamespace() . ", Name=" . $newListElement->getEntityName() . ", Value=" . $newListElement->getValue() . "<br>";
        }
        //exit("processFormNodeByType; formValue=".$formValue);

        if( !$noflush ) {
            $this->em->persist($newListElement);
            $this->em->flush($newListElement); //testing
        }

        return $newListElement;
    }

    //check if value is userWrapper case (object=PathologyResultSignatoriesList)
    public function processFormNodeValue( $formNode, $receivingEntity, $formNodeValue ) {

        if( $receivingEntity && $formNode->getObjectType() && $formNode->getEntityName() == "PathologyResultSignatoriesList" ) {

            $creator = $this->sc->getToken()->getUser();
            $transformer = new GenericTreeTransformer($this->em, $creator, "PathologyResultSignatoriesList", "UserdirectoryBundle");
            //$userWrapperTransformer = new SingleUserWrapperTransformer($this->em, $this->container, $creator, 'UserWrapper');

            //$value = vib9002,2,oli2002
            $valueArr = $receivingEntity->getIdValues();
            //$valueArr = explode(",",$values);

            $resArr = array();

            foreach( $valueArr as $value ) {
                //convert all to PathologyResultSignatoriesList's id
                $dropdownObject = $transformer->reverseTransform($value);
                if( $dropdownObject ) {
                    //echo "dropdownObject id=".$dropdownObject->getId()."<br>";
                    $resArr[] = $dropdownObject->getId();
                }
            }

            return implode(',',$resArr);
        }

        if( $receivingEntity && $formNode->getObjectTypeName() == "Form Field - Checkboxes" ) {
            $valueArr = $receivingEntity->getIdValues();
            //return implode(',',$valueArr);
            return $valueArr;
        }

        if( $receivingEntity ) {
            if (
                $formNode->getObjectTypeName() == "Form Field - Dropdown Menu - Allow Multiple Selections" ||
                $formNode->getObjectTypeName() == "Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries" ||
                $formNode->getObjectTypeName() == "Form Field - Dropdown Menu - Allow New Entries"
            ) {
                $valueArr = $receivingEntity->getIdValues();
                return implode(',',$valueArr);
                //return $valueArr;
            }
        }


        return $formNodeValue;
    }

    public function hasValue( $formNode ) {

        $formNodeTypeName = $formNode->getObjectType()->getName()."";
        //echo "formNodeTypeName=" . $formNodeTypeName . "<br>";

        if( $formNodeTypeName == "Form Group" ) {
            return false;
        }
        if( $formNodeTypeName == "Form" ) {
            return false;
        }
        if( $formNodeTypeName == "Form Section" ) {
            return false;
        }
        if( $formNodeTypeName == "Form Section Array" ) {
            return false;
        }

        $formNodeType = $formNode->getObjectType();
        //echo "formNodeType: ".$formNodeType." <br>";
        $receivedValueEntityNamespace = $formNodeType->getReceivedValueEntityNamespace();
        $receivedValueEntityName = $formNodeType->getReceivedValueEntityName();
        //echo "entity: $receivedValueEntityNamespace:$receivedValueEntityName <br>";
        if( !$receivedValueEntityNamespace || !$receivedValueEntityName ) {
            return false;
        }

        return true;
    }

    public function createNewList( $formNode ) {
        $userSecUtil = $this->container->get('user_security_utility');
        $formNodeObjectType = $formNode->getObjectType();
        //$entityNamespace = $formNodeObjectType->getEntityNamespace();
        //$entityName = $formNodeObjectType->getEntityName();
        $receivedValueEntityNamespace = $formNodeObjectType->getReceivedValueEntityNamespace();
        $receivedValueEntityName = $formNodeObjectType->getReceivedValueEntityName();

        if( !$receivedValueEntityNamespace || !$receivedValueEntityName ) {
            //exit("exit: entity name is null");
            return null;
        }

        //Oleg\UserdirectoryBundle\Entity:ObjectTypeText
        //"OlegUserdirectoryBundle:ObjectTypeText"
//        $entityNamespaceArr = explode("\\",$entityNamespace);
//        if( count($entityNamespaceArr) > 2 ) {
//            $entityNamespaceShort = $entityNamespaceArr[0] . $entityNamespaceArr[1];
//            $entityFullName = $entityNamespaceShort . ":" . $entityName;
//        } else {
//            throw new \Exception( 'Corresponding value list namespace is invalid: '.$entityNamespace );
//        }

        $listClassName = $receivedValueEntityNamespace."\\".$receivedValueEntityName;
        $newListElement = new $listClassName();
        //$newListElement = new ObjectTypeText();
        $creator = $this->sc->getToken()->getUser();
        $name = "";
        $count = null;
        $userSecUtil->setDefaultList($newListElement,$count,$creator,$name);

        return $newListElement;
    }

    //assume only nodes of type "Form" can be attached to the $formNodeHolderEntity (MessageCategory)
    public function getAllRealFormNodes( $formNodeHolderEntity ) {
        $formNodes = array();
        //assume only one form attached to the message category holder
        $holderForms = $formNodeHolderEntity->getFormNodes();
        if( count($holderForms) > 0 ) {
            $formNodes = $this->getRecursionAllFormNodes($holderForms->first(),$formNodes,'real');
        }
        return $formNodes;
    }
    public function getRecursionAllFormNodes( $formNode, $formNodes, $type ) {
        $children = $formNode->getChildren();
        if( $type == 'real' ) {
            if( $this->hasValue($formNode) ) {
                $formNodes[] = $formNode;
            }
        }
        if( $type == 'section' ) {
            if( $this->isValidFormSection($formNode) ) {
                $formNodes[] = $formNode;
            }
        }
        foreach( $children as $formNodeChild ) {
            $formNodes = $this->getRecursionAllFormNodes( $formNodeChild, $formNodes, $type );
        }
        return $formNodes;
    }

    //testing case:
    // http://localhost/order/directory/formnode-fields/?holderNamespace=Oleg\OrderformBundle\Entity&holderName=MessageCategory&holderId=32&cycle=new&testing=true
    // http://localhost/order/directory/formnode-fields/?holderNamespace=Oleg\OrderformBundle\Entity&holderName=MessageCategory&holderId=34&cycle=new&testing=true
    //get the same top form section by name and objectTypeId
    public function getTopFormSectionByHolderTreeRecursion( $formNodeHolder, $formNodeName, $objectTypeId, $testing=false ) {
        if( $testing ) {
            echo "topParentFormSection: holder=" . $formNodeHolder->getName() . " ($formNodeName, $objectTypeId)" . "<br>";
        }
        if( $formNodeHolder->getParent() ) {
            //echo "parent holder=".$formNodeHolder."<br><br>";
            $topFormSection = $this->getTopFormSectionByHolderTreeRecursion($formNodeHolder->getParent(),$formNodeName,$objectTypeId);
            if( $topFormSection ) {
                //exit('topFormSection='.$topFormSection);
                return $topFormSection;
            } else {
                return $this->getHolderValidSection($formNodeHolder, $formNodeName, $objectTypeId, $testing);
            }
        } else {
            if( $formNodeHolder ) {
                return $this->getHolderValidSection($formNodeHolder, $formNodeName, $objectTypeId, $testing);
            }
        }
        //exit('no parent!');
        return null;
    }
    public function getHolderValidSection( $formNodeHolder, $formNodeName, $objectTypeId, $testing=false ) {
        //echo "### Holder=".$formNodeHolder->getName()."<br>";
        $formSections = $this->getValidFormSections($formNodeHolder);
        //echo "### formsections=".count($formSections)."<br>";
        foreach( $formSections as $formSection ) {
            if( $this->isValidFormSection($formSection) ) {
                if( $testing ) {
                    echo "form section=" . $formSection . ": " . $formSection->getName() . "?=" . $formNodeName . "; ";
                    echo $formSection->getObjectTypeId() . "?=" . $objectTypeId . "<br>";
                }
                //check if name and object type are the same
                if( $formSection->getName() == $formNodeName  ) {
                    if( $formSection->getObjectTypeId() == $objectTypeId ) {
                        return $formSection;
                    }
                }
            }
        }
    }

    //check if node is visible and "Form Section" or "Form Section Array"
    public function isValidFormSection( $formNode ) {
        $formNodeObjectTypeName = $formNode->getObjectTypeName();
        if( $formNodeObjectTypeName && $formNode->isVisible() ) {
            //echo "formNodeObjectTypeName=".$formNodeObjectTypeName."<br>";
            if(
                $formNodeObjectTypeName == "Form Section" ||
                $formNodeObjectTypeName == "Form Section Array"
            ) {
                return true;
            }
        }
        return false;
    }

    public function getValidFormSections( $formNodeHolder ) {
        //echo "getValidFormSections formNodeHolder=".$formNodeHolder->getName()."<br>";
        $formNodes = array();
        //assume only one form attached to the message category holder
        $holderForms = $formNodeHolder->getFormNodes();
        if( count($holderForms) > 0 ) {
            $formNodes = $this->getRecursionAllFormNodes($holderForms->first(),$formNodes,'section');
        }
        return $formNodes;

//        $formSections = array();
//        foreach( $formNodeHolder->getFormNodes() as $formNode ) {
//            echo "form node=".$formNode."<br>";
//            if( $this->isValidFormSection($formNode) ) {
//                $formSections[] = $formNode;
//            }
//        }
//        return $formSections;
    }

    public function getArraySectionCount( $formNode, $arraySectionCount, $testing ) {
        //only if at least one parent is array section
        if( !$this->isUnderArraySectionRecursion($formNode,$testing) ) {
            return null;
        }

        $arraySectionCount = $this->getArraySectionCountRecursive($formNode,$arraySectionCount,$testing);
        //append flag string to the $arraySectionCount separated by underscore '_'
        $arraySectionCount = $this->gePrefixedtArraySectionCount($arraySectionCount);

        return $arraySectionCount;
    }
    //get section array index only for section array formnode
    //get array section count: 0-1 means that this array section has an index '1' (alos there is a preceding sibling with index '0') and a parent index '0'
    public function getArraySectionCountRecursive( $formNode, $arraySectionCount, $testing ) {

        if( $testing ) {
            //echo $formNode->getId().": input=" . $arraySectionCount . "<br>";
        }

        $parentFormNode = $formNode->getParent();
        if( $parentFormNode ) {
            $arraySectionCount = $this->getArraySectionCountRecursive( $parentFormNode, $arraySectionCount, $testing );
        } else {
            if( $testing ) {
                //echo "no parent => calculate index<br>";
            }
        }

        $count = $this->getSiblingIndexByType($formNode);

        $formNodeTypeName = $formNode->getObjectTypeName();
        if( $testing ) {
            //echo "Parent formNodeTypeName=" . $formNodeTypeName . "<br>";
            //echo "Sibling count=" . $count . "<br>";
        }

        //check if parent is array section
        if( $formNodeTypeName == "Form Section Array" ) {
//            if( $arraySectionCount == null ) {
//                $arraySectionCount = '0';
//            }
            //attach index by preceding siblings
            if( $arraySectionCount === null || $arraySectionCount === '' ) {
                $arraySectionCount = $count;
            } else {
                $arraySectionCount = $arraySectionCount . "-" . $count;
            }
        }

        if( $testing ) {
            //echo $formNode->getId().": output=" . $arraySectionCount . "<br>";
        }

        return $arraySectionCount;
    }

    public function isUnderArraySectionRecursion($formNode,$testing=null) {
        if( $formNode && $formNode->getObjectTypeName() == "Form Section Array" ) {
            if( $testing ) {
                echo $formNode->getId().": FormNode is Section Array";
            }
            return true;
        }

        $parent = $formNode->getParent();
        if( $parent ) {
            if( $parent == "Form Section Array" ) {
                return true;
            } else {
                return $this->isUnderArraySectionRecursion($parent,$testing);
            }
        }
        return false;
    }

    public function getArraySectionPrefix() {
        return "fffsa";
    }

    //fffsa_0-0_fffsa => 0-0
    public function getCleanedArraySection( $arraySectionCount ) {
        $prefix = $this->getArraySectionPrefix();
        $arraySectionCount = str_replace($prefix.'_', '', $arraySectionCount);
        $arraySectionCount = str_replace('_'.$prefix, '', $arraySectionCount);
        return $arraySectionCount;
    }

    //0-0 => fffsa_0-0_fffsa
    public function gePrefixedtArraySectionCount( $cleanArraySectionCount ) {
        $prefix = $this->getArraySectionPrefix();
        $arraySectionCount = $prefix . '_' . $cleanArraySectionCount . '_' . $prefix;
        return $arraySectionCount;
    }

    //get order index of all siblings on the same level ordered by orderinlist
    public function getSiblingIndexByType( $node ) {

        $index = 0;
        return $index; //testing: we can have only unique sections on the form, so use index 0 for all sections

        $parent = $node->getParent();

        if( !$parent ) {
            return $index;
        }

        foreach( $parent->getChildren() as $sibling ) {

            //check index if object type is the same
            if( $node->getObjectTypeId() == $sibling->getObjectTypeId() ) {

                if( $sibling->getId() == $node->getId() ) {
                    return $index;
                }

                $index++;
            }
        }

        return $index;
    }

    //Simplifying version getting form node holder (messageCategory) form nodes info (i.e. "Impression/Outcome: This is an example of an impression and outcome.")
    //$holderEntity is the holder of the $formNodeHolderEntity, for example, Message entity
    //$formNodeHolderEntity is a form node holder, for example, MessageCategory entity
    public function getFormNodeHolderShortInfo( $holderEntity, $formNodeHolderEntity, $separator="<br>" ) {

        if( !$holderEntity ) {
            return null;
        }

        if( !$formNodeHolderEntity ) {
            return null;
        }

        $class = new \ReflectionClass($holderEntity);
        $className = $class->getShortName();
        $classNamespace = $class->getNamespaceName();
        $mapper = array(
            'entityNamespace' => $classNamespace,   //"Oleg\\OrderformBundle\\Entity",
            'entityName' => $className, //"Message",
            'entityId' => $holderEntity->getId(),
        );
        $entityId = $holderEntity->getId(); //"Message ID";

        //$formNodeUtil = $this->container->get('user_formnode_utility');
        //$formNodeHolderId = $formNodeHolderEntity->getId();
        $resArr = array();

        //$formNodes = $formNodeHolderEntity->getFormNodes();
        //get only 'real' fields as $formNodes
        $formNodes = $this->getAllRealFormNodes($formNodeHolderEntity);

        foreach( $formNodes as $formNode ) {

            if( $formNode && $formNode->getId() ) {
                $formNodeId = $formNode->getId();
            } else {
                continue;
            }

//            if( $this->isFormNodeInArray($formNodeId,$resArr) ) {
//                continue;
//            }

            $formNodeValue = null;
            $receivingEntity = null;
            if( $entityId ) {
                $complexRes = $this->getFormNodeValueByFormnodeAndReceivingmapper($formNode,$mapper);
                $formNodeValue = $complexRes['formNodeValue'];
                $receivingEntity = $complexRes['receivingEntity'];
            }

            //echo "formNodeValue for formNode=".$formNode->getId().":<br>";

            if( is_array($formNodeValue) ) {
                //not implemented
            } else {
                //////////////// Regular form node /////////////////////
                //process userWrapper case
                $formNodeValue = $this->processFormNodeValue($formNode,$receivingEntity,$formNodeValue);
            }

            $resArr[] = $formNode->getName() . ": " . $formNodeValue;
        }

        return implode($separator,$resArr);
    }


//    public function getListByType( $formNode ) {
//
//        $list = null;
//        $newListElement = null;
//
//        $formNodeType = $formNode->getObjectType();
//        //echo "formNodeType=" . $formNodeType . "<br>";
//
//        if( $formNodeType->getName()."" == "Form Field - Free Text" ) {
//            $list = $formNode->getObjectTypeText();
//            $newListElement = new ObjectTypeText();
//            $creator = $this->sc->getToken()->getUser();
//            $name = "";
//            $count = null;
//            $entityFullName = "OlegUserdirectoryBundle:ObjectTypeText";
//            $this->setDefaultList($newListElement,$count,$creator,$name,$entityFullName);
//            $this->em->persist($newListElement);
//        }
//
//        $res = array(
//            'list' => $list,
//            'newList' => $newListElement
//        );
//
//        return $res;
//    }

//    public function setDefaultList( $entity, $count, $user, $name=null, $entityFullName ) {
//
//        if( !$count ) {
//            $count = $this->getMaxId($entityFullName);
//            //echo "count=".$count."<br>";
//        }
//
//        $entity->setOrderinlist( $count );
//        $entity->setCreator( $user );
//        $entity->setCreatedate( new \DateTime() );
//        $entity->setType('user-added');
//        if( $name ) {
//            $entity->setName( trim($name) );
//        }
//        return $entity;
//    }
//
//    public function getMaxId( $entityFullName ) {
//        //echo "entityFullName=" . $entityFullName . "<br>";
//        $repository = $this->em->getRepository($entityFullName);
//        $dql =  $repository->createQueryBuilder("u");
//        $dql->select('MAX(u.id) as idMax');
//        //$dql->setMaxResults(1);
//        $res = $dql->getQuery()->getSingleResult();
//        $maxId = $res['idMax'];
//        if( !$maxId ) {
//            $maxId = 0;
//        }
//
//        return $maxId;
//    }










    public function createV2FormNode( $params ) {
        $em = $this->em;
        $userSecUtil = $this->container->get('user_security_utility');
        $username = $this->container->get('security.context')->getToken()->getUser();

        $objectType = $params['objectType'];
        $name = $params['name'];
        $parent = $params['parent'];

        //classNamespace
        if( array_key_exists('classNamespace', $params) ) {
            $classNamespace = $params['classNamespace'];
        } else {
            $classNamespace = null;
        }

        //className
        if( array_key_exists('className', $params) ) {
            $className = $params['className'];
        } else {
            $className = null;
        }

        //classObject
        if( array_key_exists('classObject', $params) ) {
            $classObject = $params['classObject'];
        } else {
            $classObject = null;
        }

        //visible
//        if( array_key_exists('visible', $params) ) {
//            $visible = $params['visible'];
//        } else {
//            $visible = true;
//        }

        //showLabel - default true
        if( array_key_exists('showLabel', $params) ) {
            $showLabel = $params['showLabel'];
        } else {
            $showLabel = true;
        }

        //find by name and by parent ($parent) if exists
        if( $parent ) {
            $mapper = array(
                'prefix' => "Oleg",
                'className' => "FormNode",
                'bundleName' => "UserdirectoryBundle"
            );
            $node = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent($name,$parent,$mapper);
        } else {
            exit("Parent must exist!");
            $node = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName($name);
        }

        if( $node ) {
            if( $node->getType() == 'disabled' || $node->getType() == 'draft' ) {
                exit("The node $name already exists, but it has ".$node->getType()." type.");
            }
        }

        if( !$node ) {
            $node = new FormNode();

            $userSecUtil->setDefaultList($node,null,$username,$name);

            //set level
            $parentLevel = intval($parent->getLevel());
            $level = $parentLevel + 1;
            $node->setLevel($level);

            //set objectType
            if( $objectType ) {
                $node->setObjectType($objectType);
            }

            //set parent
            if( $parent ) {
                $em->persist($parent);
                $parent->addChild($node);
            }

            //set visible
            //$node->setVisible($visible);

            //set showLabel
            $node->setShowLabel($showLabel);

            if( $classNamespace && $className ) {
                $node->setEntityNamespace($classNamespace);
                $node->setEntityName($className);
            }

            if( $classObject ) {
                $node->setObject($classObject);
            }

            //echo "Created: ".$node->getName()."<br>";
            $em->persist($parent);
            $em->persist($node);
            $em->flush();

        } else {

            //update node

            //Do not update the form node if already exist
            //return;

            $updated = false;
            //echo "Existed: ".$node->getName()."<br>";
            //echo "objectType=".$objectType->getName()."<br>";

            //set objectType
            if( $objectType ) {
                //Do not change object type if already exists
                //if( !$node->getObjectType() ) {
                    $node->setObjectType($objectType);
                    $updated = true;
                //}
            }

            //set visible
            //$node->setVisible($visible);

            //set showLabel
            $node->setShowLabel($showLabel);

            if( $classNamespace && $className ) {
                $node->setEntityNamespace($classNamespace);
                $node->setEntityName($className);
                //echo "set className $classNamespace $className <br>";
                $updated = true;
            } else {
                $node->setEntityNamespace(null);
                $node->setEntityName(null);
                //echo "set NULL EntityName <br>";
                $updated = true;
            }

            if( $classObject ) {
                //echo "set  classObject=".$classObject." <br>";
                $node->setObject($classObject);
                $updated = true;
            }

            //pre-set
            if(0) {
                $node->setEntityNamespace(null);
                $node->setEntityName(null);
                $node->setEntityId(null);

                $node->setReceivedValueEntityNamespace(null);
                $node->setReceivedValueEntityName(null);
                $node->setReceivedValueEntityId(null);
                $updated = true;
            }

            if( $updated ) {
                //echo "update node=".$node." <br>";
                $em->persist($node);
                $em->flush($node);
            }

        }//if !$node

        return $node;
    }





    public function addNestedsetNodeRecursevely($parentCategory,$categories,$level,$username,$count) {

        $userSecUtil = $this->container->get('user_security_utility');
        $em = $this->em;

        foreach( $categories as $category=>$subcategory ) {

            $name = $category;

            if( $subcategory && !is_array($subcategory) ) {
                $name = $subcategory;
            }

            //find by name and by parent ($parentCategory) if exists
            if( $parentCategory ) {
                $mapper = array(
                    'prefix' => "Oleg",
                    'className' => "FormNode",
                    'bundleName' => "UserdirectoryBundle"
                );
                $node = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent($name,$parentCategory,$mapper);
            } else {
                $node = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName($name);
            }

            if( !$node ) {
                //make category
                $node = new FormNode();

                $userSecUtil->setDefaultList($node,$count,$username,$name);
                $node->setLevel($level);

//                //try to get default group by level
//                if( !$node->getOrganizationalGroupType() ) {
//                    if( $node->getLevel() ) {
//                        $messageTypeClassifier = $em->getRepository('OlegOrderformBundle:MessageTypeClassifiers')->findOneByLevel($node->getLevel());
//                        if ($messageTypeClassifier) {
//                            $node->setOrganizationalGroupType($messageTypeClassifier);
//                        }
//                    }
//                }

                $count = $count + 10;
            }

//            echo $level.": category=".$name.", count=".$count."<br>";
//            echo "subcategory:<br>";
//            print_r($subcategory);
//            echo "<br><br>";
//            echo "messageCategory=".$node->getName()."<br>";

            //add to parent
            if( $parentCategory ) {
                $em->persist($parentCategory);
                $parentCategory->addChild($node);
            }

            //$node->printTree();

            //make children
            if( $subcategory && is_array($subcategory) && count($subcategory) > 0 ) {
                $count = $this->addNestedsetNodeRecursevely($node,$subcategory,$level+1,$username,$count);
            }

            //testing
            if( 0 ) {
                $label = null;
                if ($node->getObjectType()) {
                    $label = $node->getObjectType()->getName();
                } else {
                    $label = null;
                }
                if ($node->getParent()) {
                    $parent = $node->getParent()->getName();
                } else {
                    $parent = null;
                }
                echo $node.": label=".$label."; level=".$node->getLevel()."; parent=".$parent."<br>";
            }

            $em->persist($node);
            $em->flush();
        }

        return $count;
    }
    public function getObjectTypeByName($objectTypeName) {
        $em = $this->em;
        $objectType = $em->getRepository('OlegUserdirectoryBundle:ObjectTypeList')->findOneByName($objectTypeName);
        if( !$objectType ) {
            throw new \Exception( "ObjectType not found by ".$objectTypeName );
        }
        return $objectType;
    }

    public function setMessageCategoryListLink( $messageCategoryName, $formNode, $parentMessageCategoryName=null ) {
        //set this formnode to the MessageCategory Entity Name
        $em = $this->em;
        $messageCategory = null;

        $messageCategories = $em->getRepository('OlegOrderformBundle:MessageCategory')->findByName($messageCategoryName);
        if( count($messageCategories) == 0 ) {
            exit("Message categories not found by name=".$messageCategoryName);
        }

        if( count($messageCategories) > 1 ) {
            if( $parentMessageCategoryName ) {
                foreach ($messageCategories as $thisMessageCategory) {
                    if( $thisMessageCategory->getParent() && $thisMessageCategory->getParent()->getName() == $parentMessageCategoryName ) {
                        $messageCategory = $thisMessageCategory;
                        break;
                    }
                }
            } else {
                exit("Multiple Message categories found (".count($messageCategories).") by name=".$messageCategoryName);
            }
        }

        if( count($messageCategories) == 1 ) {
            $messageCategory = $messageCategories[0];
        }

        if( !$messageCategory ) {
            exit("Message category is null");
        }

        //clear all old form nodes
        $messageCategory->clearFormNodes();

        //clear old List Object values
        $messageCategory->clearObject();

        if( $formNode ) {
            $messageCategory->addFormNode($formNode);
        }

        $em->persist($messageCategory);
        $em->flush();
    }






    public function getTimezones() {
        $tzUtil = new TimeZoneUtil();
        return $tzUtil->tz_list();
    }

    public function getDropdownValue( $formNode, $outputType=null ) {
        $em = $this->em;
        $output = array();

        //IF: the ObjectType is "Form Field - Dropdown Menu" => the linked list is dropdown values
        $objectType = $formNode->getObjectType();
        if( !$objectType ) {
            //it must be dropdown object type.
            return $output;
        }

//        $objectTypeName = $objectType->getName()."";
//        if(
//            $objectTypeName != "Form Field - Dropdown Menu" &&
//            $objectTypeName != "Form Field - Dropdown Menu - Allow Multiple Selections" &&
//            $objectTypeName != "Form Field - Radio Button" &&
//            $objectTypeName != "Form Field - Month" &&
//            $objectTypeName != "Form Field - Day of the Week"
//        ) {
//            //it must be only dropdown object type.
//            //echo '#########not valid object type: '.$objectTypeName."#########<br>";
//            return $output;
//        }

        $entityNamespace = $formNode->getEntityNamespace(); //"Oleg\OrderformBundle\Entity"
        $entityName = $formNode->getEntityName();           //"BloodProductTransfusedList"

        if( $entityNamespace && $entityName ) {

            $entityNamespaceArr = explode("\\",$entityNamespace);
            $bundleName = $entityNamespaceArr[0].$entityNamespaceArr[1];

            $query = $em->createQueryBuilder()->from($bundleName.':'.$entityName, 'list')
                //->select("list.id as id, list.name as text")
                ->select("list")
                ->orderBy("list.orderinlist","ASC");

            //$query->where("list.type = :typedef OR list.type = :typeadd")->setParameters(array('typedef' => 'default','typeadd' => 'user-added'));
            $query->where("list.type = :typedef OR list.type = :typeadd")->
                    setParameters(array('typedef' => 'default','typeadd' => 'user-added'));

            $output = $query->getQuery()->getResult();

        } else {
            return $output;
        }

        $resArr = array();
        foreach( $output as $list ) {
            if( $entityName == "LabResultNameList" ) {
                $text = $list->getOptimalNameShortnameAbbreviation();
            } else {
                $text = $list->getOptimalAbbreviationName();
            }
            //echo "list id=".$list['id']."; text=".$list['text']."<br>";
            $resArr[] = array(
                'id' => $list->getId(),
                'text' => $text
            );
        }

        //get additional menu children "Dropdown Menu Value"
        foreach( $formNode->getChildren() as $dropdownValue ) {
            if( $entityName == "LabResultNameList" ) {
                $text = $dropdownValue->getOptimalNameShortnameAbbreviation();
            } else {
                $text = $dropdownValue->getOptimalAbbreviationName();
            }
            $resArr[] = array(
                'id' => $dropdownValue->getId(),
                'text' => $text
            );
        }

        if( $outputType == 'json' ) {
            return json_encode($resArr);
        }

        return $resArr;
    }

    public function getDefaultValue( $formNode ) {
        $em = $this->em;
        $entityNamespace = $formNode->getEntityNamespace(); //"Oleg\OrderformBundle\Entity"
        $entityName = $formNode->getEntityName();           //"CCIUnitPlateletCountDefaultValueList"
        $entityId = $formNode->getEntityId();

        if( $entityNamespace && $entityName && $entityId ) {
            $entityNamespaceArr = explode("\\", $entityNamespace);
            $bundleName = $entityNamespaceArr[0] . $entityNamespaceArr[1];
            $defaultValueEntity = $em->getRepository($bundleName.':'.$entityName)->find($entityId);

            if( $defaultValueEntity ) {
                return $defaultValueEntity->getName() . "";
            }
        }

        return null;
    }

    public function getFormNodeReceivedListRepository( $formNode ) {
        $formNodeType = $formNode->getObjectType();
        //echo "formNodeType: ".$formNodeType." <br>";
        if( !$formNodeType ) {
            return null;
        }
        $receivedValueEntityNamespace = $formNodeType->getReceivedValueEntityNamespace(); //Oleg\UserdirectoryBundle\Entity
        $receivedValueEntityName = $formNodeType->getReceivedValueEntityName(); //ObjectTypeText
        //echo "entity: $receivedValueEntityNamespace:$receivedValueEntityName <br>";
        if( !$receivedValueEntityNamespace || !$receivedValueEntityName ) {
            return null;
        }

        $receivedValueEntityNamespaceArr = explode("\\", $receivedValueEntityNamespace);
        $bundleName = $receivedValueEntityNamespaceArr[0] . $receivedValueEntityNamespaceArr[1];
        $repo = $this->em->getRepository($bundleName.':'.$receivedValueEntityName);

        return $repo;
    }

    public function getFormNodeValueByFormnodeAndReceivingmapper( $formNode, $mapper, $asObject=false ) {

        if( !$formNode ) {
            return null;
        }

        if( !$mapper || count($mapper) == 0 ) {
            return null;
        }

//        $class = new \ReflectionClass($object);
//        $className = $class->getShortName();          //ObjectTypeText
//        $classNamespace = $class->getNamespaceName(); //Oleg\UserdirectoryBundle\Entity

        //echo "classNamespace=".$classNamespace."<br>";
        //echo "className=".$className."<br>";
        //echo "entityId=".$object->getId()."<br>";
        //print_r($mapper);

        $treeRepository = $this->getFormNodeReceivedListRepository($formNode);
        //$treeRepository = $this->em->getRepository($mapper['prefix'].$mapper['bundleName'].':'.$mapper['className']);

        $dql =  $treeRepository->createQueryBuilder("list");
        $dql->select('list');
        $dql->where('list.entityName = :entityName AND list.entityNamespace = :entityNamespace AND list.entityId = :entityId');
        $dql->andWhere('list.formNode = :formNodeId');
        $dql->orderBy('list.arraySectionIndex','DESC');
        $dql->addOrderBy('list.orderinlist', 'ASC');

        $query = $this->em->createQuery($dql);

        //echo "query=".$query->getSql()."<br>";

        $query->setParameters(
            array(
                'entityName' => $mapper['entityName'],
                'entityNamespace' => $mapper['entityNamespace'],
                'entityId' => $mapper['entityId'],
                'formNodeId' => $formNode->getId()
            )
        );

        $results = $query->getResult();
        //echo "count=".count($results)."<br>";

        if( $asObject ) {
            return $results;
        }

        if( count($results) == 1 ) {
            //return $results[0]->getValue();
            $formNodeValue =  $this->getFormNodeValueByType($formNode,$results[0]);
            $complexRes = array(
                'formNodeValue' => $formNodeValue,
                'receivingEntity' => $results[0]
            );
            return $complexRes;
        }

        if( count($results) > 1 ) {
            $resArr = array();
            foreach( $results as $result ) {
                //$resArr[] = $result->getValue();
                //$resArr[] = $this->getFormNodeValueByType($formNode,$result);
                //$resArr['formNodeValue'] = $this->getFormNodeValueByType($formNode,$result);
                $res = array(
                    'formNodeValue' => $this->getFormNodeValueByType($formNode,$result),
                    'formNodeId' => $formNode->getId(),
                    'arraySectionId' => $result->getArraySectionId(),
                    'arraySectionIndex' => $result->getArraySectionIndex(),
                );
                $resArr[] = $res;
            }
            //return $resArr;
            $complexRes = array(
                'formNodeValue' => $resArr,
                'receivingEntity' => null
            );
            return $complexRes;
        }

        return null;
    }
    public function getFormNodeValueByType( $formNode, $list ) {
        $formNodeType = $formNode->getObjectType();
        if( $formNodeType ) {
            $formNodeTypeName = $formNodeType->getName()."";
            //echo "############type=[".$formNodeTypeName."]###############<br>";
            if( $formNodeTypeName == "Form Field - Time" ) {
                return $list->getTimeValue();
            }
        }
        return $list->getValue();
    }

    public function getFormNodeIdWithSectionCount($formNodeId,$arraySectionCount) {
        if( $arraySectionCount ) {
            //testing
            return $formNodeId . '_' . $arraySectionCount;
        }
        return $formNodeId;
    }







    ////////////////////////// pre-generate FormNode tree /////////////////////////////////
    //run: order/directory/admin/list/generate-form-node-tree/
    public function generateFormNode() {

        $em = $this->em;
        $username = $this->container->get('security.context')->getToken()->getUser();

        //root
        $categories = array(
            'All Forms' => array('Pathology Call Log Book'),
        );
        $count = 10;
        $level = 0;
        $count = $this->addNestedsetNodeRecursevely(null,$categories,$level,$username,$count);
        $parentNode = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName('Pathology Call Log Book');
        //echo "rootNode=".$parentNode."<br>";

        //create Pathology Call Log Book

        //Create separate "Form" node for each Message Category.
        // "Form Group" and "Form" nodes are always hidden.
        // "Form Section" is always visible.
        $count = 0;

        //use https://bitbucket.org/weillcornellpathology/call-logbook-plan/issues/30/new-entry-message

        // Pathology Call Log Entry
        //$PathologyCallLogEntry = $this->createPathologyCallLogEntryFormNode($parentNode);
        //echo "PathologyCallLogEntry=".$PathologyCallLogEntry."<br>";
        $this->createV2PathologyCallLogEntryFormNode($parentNode);
        $count++;

        // Transfusion Medicine
        //$TransfusionMedicine = $this->createTransfusionMedicine($parentNode);
        //echo "TransfusionMedicine=".$TransfusionMedicine."<br>";
        $this->createV2TransfusionMedicine($parentNode);
        $count++;

        $this->createAfterFirstdoseplasma($parentNode);
        $count++;

        //exit('EOF message category');

        return round($count);
    }

    public function addFormToHolder( $parent, $holderName, $sections, $parentMessageCategoryName=null ) {
        $objectTypeForm = $this->getObjectTypeByName('Form');
        $objectTypeFormSection = $this->getObjectTypeByName('Form Section');

        if( $holderName ) {
            //Create form: i.e. Transfusion Medicine -> Third+ dose platelets [Message Category]
            $formParams = array(
                'parent' => $parent,
                'name' => $holderName,
                'objectType' => $objectTypeForm,
            );
            $parentForm = $this->createV2FormNode($formParams);
            $this->setMessageCategoryListLink($holderName, $parentForm, $parentMessageCategoryName);
        } else {
            $parentForm = $parent;
        }

        foreach( $sections as $section ) {
            $sectionName = $section['sectionName'];

            if( array_key_exists('sectionObjectTypeName', $section) ) {
                $objectTypeSection = $this->getObjectTypeByName($section['sectionObjectTypeName']);
                if( !$objectTypeSection ) {
                    exit('Object type not found by name='.$section['sectionObjectTypeName']);
                }
            } else {
                $objectTypeSection = $objectTypeFormSection;
            }

            if( array_key_exists('sectionParentName', $section) ) {
                $thisParentForm = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName($section['sectionParentName']);
                if( !$thisParentForm ) {
                    exit('Parent form not found by name='.$section['sectionParentName']);
                }
            } else {
                $thisParentForm = $parentForm;
            }

            if( $sectionName ) {
                $formParams = array(
                    'parent' => $thisParentForm,
                    'name' => $sectionName,
                    'objectType' => $objectTypeSection,
                );
                $sectionObject = $this->createV2FormNode($formParams);
            } else {
                $sectionObject = $thisParentForm;
            }

            $fields = $section['fields'];
            foreach( $fields as $fieldName=>$objectTypeNameParam ) {
                if( is_array($objectTypeNameParam) ) {
                    //'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
                    //'className' => "BloodTypeList"
                    $objectTypeName = $objectTypeNameParam[0];
                    $classNamespace = $objectTypeNameParam[1];
                    $className = $objectTypeNameParam[2];
                } else {
                    $objectTypeName = $objectTypeNameParam;
                    $classNamespace = null;
                    $className = null;
                }

                $objectType = $this->getObjectTypeByName($objectTypeName);
                if( !$objectType ) {
                    exit('object type not found by name='.$objectTypeName);
                }

                $fieldParams = array(
                    'parent' => $sectionObject,
                    'name' => $fieldName,
                    'objectType' => $objectType,
                    'classNamespace' => $classNamespace,
                    'className' => $className
                );
                $this->createV2FormNode($fieldParams);
            }//foreach

        }//foreach

        return $thisParentForm;
    }

    public function createAfterFirstdoseplasma($parent) {

        $objectTypeForm = $this->getObjectTypeByName('Form');
        $objectTypeSection = $this->getObjectTypeByName('Form Section');
        $objectTypeString = $this->getObjectTypeByName('Form Field - Free Text, Single Line');

        //First dose plasma [Form Section]
        $formParams = array(
            'parent' => $parent,
            'name' => "First dose plasma",
            'objectType' => $objectTypeForm,
        );
        $parentForm = $this->createV2FormNode($formParams);
        $this->setMessageCategoryListLink("First dose plasma",$parentForm);

        $formParams = array(
            'parent' => $parentForm,
            'name' => "Laboratory Values of Interest",
            'objectType' => $objectTypeSection,
        );
        $laboratoryValues = $this->createV2FormNode($formParams);

        //     INR: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "INR",
            'objectType' => $objectTypeString,
        );
        $formField = $this->createV2FormNode($formParams);

        //    PT: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "PT",
            'objectType' => $objectTypeString,
        );
        $formField = $this->createV2FormNode($formParams);

        //    PTT: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "PTT",
            'objectType' => $objectTypeString,
        );
        $formField = $this->createV2FormNode($formParams);


        /////////////////////// Transfusion Medicine -> First dose platelets [Message Category]
        $formParams = array(
            'parent' => $parent,
            'name' => "First dose platelets",
            'objectType' => $objectTypeForm,
        );
        $parentForm = $this->createV2FormNode($formParams);
        $this->setMessageCategoryListLink("First dose platelets",$parentForm);

        //Miscellaneous [Form Section]
        $formParams = array(
            'parent' => $parentForm,
            'name' => "Miscellaneous",
            'objectType' => $objectTypeSection,
        );
        $miscellaneous = $this->createV2FormNode($formParams);

        //Medication: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Medication",
            'objectType' => $objectTypeString,
        );
        $MedicationString = $this->createV2FormNode($formParams);


        /////////////// Transfusion Medicine -> Third+ dose platelets [Message Category] ////////////////
        //Laboratory Values [Form Section]
        $sections = array(
            //CCI: [Form Field - Free Text, Single Line]
            array(
                'sectionName' => "Laboratory Values of Interest",
                'fields' => array('CCI'=>'Form Field - Free Text, Single Line')
            ),
            //Platelet Goal: [Form Field - Free Text, Single Line]
            array(
                'sectionName' => "Miscellaneous",
                'fields' => array('Platelet Goal'=>'Form Field - Free Text, Single Line')
            )
        );
        $this->addFormToHolder($parent,"Third+ dose platelets",$sections);

        /////////////////////////// Transfusion Medicine -> Cryoprecipitate [Message Category] ///////////////////////
        //    Laboratory Values [Form Section]
        //    INR: [Form Field - Free Text, Single Line]
        //    PT: [Form Field - Free Text, Single Line]
        //    PTT: [Form Field - Free Text, Single Line]
        //    Fibrinogen: [Form Field - Free Text, Single Line]
        $sections = array(
            array(
                'sectionName' => "Laboratory Values of Interest",
                'fields' => array(
                    'INR'=>'Form Field - Free Text, Single Line',
                    'PT'=>'Form Field - Free Text, Single Line',
                    'PTT'=>'Form Field - Free Text, Single Line',
                    'Fibrinogen'=>'Form Field - Free Text, Single Line',
                )
            ),
        );
        $this->addFormToHolder($parent,"Cryoprecipitate",$sections);

        ////////////////////////// Transfusion Medicine -> MTP [Message Category]
        //        Laboratory Values [Form Section]
        //    INR: [Form Field - Free Text, Single Line]
        //    PT: [Form Field - Free Text, Single Line]
        //    PTT: [Form Field - Free Text, Single Line]
        //    Fibrinogen: [Form Field - Free Text, Single Line]
        $sections = array(
            array(
                'sectionName' => "Laboratory Values of Interest",
                'fields' => array(
                    'INR'=>'Form Field - Free Text, Single Line',
                    'PT'=>'Form Field - Free Text, Single Line',
                    'PTT'=>'Form Field - Free Text, Single Line',
                    'Fibrinogen'=>'Form Field - Free Text, Single Line',
                )
            ),
        );
        $this->addFormToHolder($parent,"MTP",$sections);

        /////////////////////////// Transfusion Medicine -> Emergency release [Message Category]
        //        Miscellaneous [Form Section]
        //    Blood Type of Unit: [Form Field - Free Text, Single Line]
        //    Blood Type of Patient: [Form Field - Free Text, Single Line]
        $sections = array(
            array(
                'sectionName' => "Miscellaneous",
                'fields' => array(
                    'Blood Type of Unit'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Blood Type of Patient'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                )
            ),
        );
        $this->addFormToHolder($parent,"Emergency release",$sections);

        ////////////// Transfusion Medicine -> Payson transfusion [Message Category] ///////////////////
        $this->addFormToHolder($parent,"Payson transfusion",array());

        ////////////////////// Transfusion Medicine -> Incompatible crossmatch [Message Category]
        //        Miscellaneous [Form Section]
        //    Blood Type of Unit: [Form Field - Free Text, Single Line]
        //    Blood Type of Patient: [Form Field - Free Text, Single Line]
        //    Antibodies: [Form Field - Free Text, Single Line]
        //    Phenotype: [Form Field - Free Text, Single Line]
        //    Incompatibility: [Form Field - Free Text, Single Line]
        $sections = array(
            array(
                'sectionName' => "Miscellaneous",
                'fields' => array(
                    'Blood Type of Unit'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Blood Type of Patient'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Antibodies'=>'Form Field - Free Text, Single Line',
                    'Phenotype'=>'Form Field - Free Text, Single Line',
                    'Incompatibility'=>'Form Field - Free Text, Single Line',
                )
            ),
        );
        $this->addFormToHolder($parent,"Incompatible crossmatch",$sections);

        /////////////////// Transfusion Medicine -> Transfusion reaction [Message Category]
        $sections = array(
            //        Miscellaneous [Form Section]
            //            Blood Product Transfused: [Form Field - Dropdown Menu]
            //            Transfusion Reaction Type: [Form Field - Dropdown Menu]
            array(
                'sectionName' => "Miscellaneous",
                'fields' => array(
                    'Blood Product Transfused'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodProductTransfusedList"),
                    'Transfusion Reaction Type'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionReactionTypeList"),
                )
            ),
            //        Vitals [Form Section]
            //    Pre-Temp: [Form Field - Free Text, Single Line]
            //    Pre-HR: [Form Field - Free Text, Single Line]
            //    Pre-RR: [Form Field - Free Text, Single Line]
            //    Pre-O2 sat: [Form Field - Free Text, Single Line]
            //    Pre-BP: [Form Field - Free Text, Single Line]
            //    Post-Temp: [Form Field - Free Text, Single Line]
            //    Post-HR: [Form Field - Free Text, Single Line]
            //    Post-RR: [Form Field - Free Text, Single Line]
            //    Post-O2 sat: [Form Field - Free Text, Single Line]
            //    Post-BP: [Form Field - Free Text, Single Line]
            array(
                'sectionName' => "Vitals",
                'fields' => array(
                    'Pre-Temp'=>'Form Field - Free Text, Single Line',
                    'Pre-HR'=>'Form Field - Free Text, Single Line',
                    'Pre-RR'=>'Form Field - Free Text, Single Line',
                    'Pre-O2'=>'Form Field - Free Text, Single Line',
                    'Pre-BP'=>'Form Field - Free Text, Single Line',
                    'Post-Temp'=>'Form Field - Free Text, Single Line',
                    'Post-HR'=>'Form Field - Free Text, Single Line',
                    'Post-RR'=>'Form Field - Free Text, Single Line',
                    'Post-O2'=>'Form Field - Free Text, Single Line',
                    'Post-BP'=>'Form Field - Free Text, Single Line',
                )
            ),
            // Transfusion Reaction Workup [Form Section]
            //  Transfusion Reaction Workup Description [Form Field - Free Text]
            //  Clerical error: [Form Field - Dropdown Menu]
            //  Blood Type of Unit: [Form Field - Dropdown Menu]
            //  Blood type of pre-transfusion specimen: [Form Field - Dropdown Menu] (BloodTypeList)
            //  Blood type of post-transfusion specimen: [Form Field - Dropdown Menu]
            //  Pre-transfusion antibody screen: [Form Field - Dropdown Menu]
            //  Post-transfusion antibody screen: [Form Field - Dropdown Menu]
            //  Pre-transfusion DAT: [Form Field - Dropdown Menu]
            //  Post-transfusion DAT: [Form Field - Dropdown Menu]
            //  Pre-transfusion crossmatch: [Form Field - Dropdown Menu]
            //  Post-transfusion crossmatch: [Form Field - Dropdown Menu]
            //  Pre-transfusion hemolysis check: [Form Field - Dropdown Menu]
            //  Post-transfusion hemolysis check: [Form Field - Dropdown Menu]
            //  Microbiology: [Form Field - Free Text, Single Line]
            array(
                'sectionName' => "Transfusion Reaction Workup",
                'fields' => array(
                    'Transfusion Reaction Workup Description'=>'Form Field - Free Text, Single Line',
                    'Clerical error'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","ClericalErrorList"),
                    'Blood Type of Unit'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodProductTransfusedList"),
                    'Blood type of pre-transfusion specimen'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Blood type of post-transfusion specimen'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Pre-transfusion antibody screen'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionAntibodyScreenResultsList"),
                    'Post-transfusion antibody screen'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionAntibodyScreenResultsList"),
                    'Pre-transfusion DAT'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionDATResultsList"),
                    'Post-transfusion DAT'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionDATResultsList"),
                    'Pre-transfusion crossmatch'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionCrossmatchResultsList"),
                    'Post-transfusion crossmatch'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionCrossmatchResultsList"),
                    'Pre-transfusion hemolysis check'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionHemolysisCheckResultsList"),
                    'Post-transfusion hemolysis check'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionHemolysisCheckResultsList"),
                    'Microbiology'=>'Form Field - Free Text, Single Line',
                )
            ),

        );
        $this->addFormToHolder($parent,"Transfusion reaction",$sections);

        /////////////////// Transfusion Medicine -> Complex platelet summary [Message Category] ////////////////////
        $sections = array(
            //            Laboratory Values [Form Section]
            //    HLA A: [Form Field - Free Text, Single Line]
            //    HLA B: [Form Field - Free Text, Single Line]
            //    Rogosin PRA: [Form Field - Free Text, Single Line]
            //    Rogosin date: [Form Field - Full Date]
            //    Antibodies [Form Field - Dropdown Menu] (ComplexPlateletSummaryAntibodiesList)
            //    NYBC date: [Form Field - Full Date]
            array(
                'sectionName' => "Laboratory Values of Interest",
                'fields' => array(
                    'HLA A'=>'Form Field - Free Text, Single Line',
                    'HLA B'=>'Form Field - Free Text, Single Line',
                    'Rogosin PRA'=>'Form Field - Free Text, Single Line',
                    'Rogosin date'=>'Form Field - Full Date',
                    'Antibodies'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","ComplexPlateletSummaryAntibodiesList"),
                    'NYBC date'=>'Form Field - Full Date',
                )
            ),
            //CCI (Corrected Count Increment) Calculations: [Form Section]
            //    BSA: [Form Field - Free Text, Single Line]
            //    Unit Platelet Count [Form Field - Free Text, Single Line] : USE "Link To List ID" to link to a new list titled "CCI Unit Platelet Count Default Value" with one list item with "3" in the name column, load "3" via this link into this field on load. This mechanism will allow multiple possible default values for a given field depending on rules (once rules are implemented, until then your logic should grab the first value on the list).
            //        CCI Unit Platelet Count Default Value [Free Text Field Default Value List]
            //            3 [Free Text Field Default Value]
            array(
                'sectionName' => "CCI (Corrected Count Increment) Calculations",
                'fields' => array(
                    'BSA'=>'Form Field - Free Text, Single Line',
                    'Unit Platelet Count'=>'Form Field - Free Text, Single Line',
                )
            ),
            //CCI (Corrected Count Increment) Instance: [Form Section] NESTED IN "CCI (Corrected Count Increment) Calculations: [Form Section]"
            //    CCI date: [Form Field - Full Date and Time]
            //    CCI Platelet Type Transfused [Form Field - Dropdown Menu]
            //    Pre Platelet Count 1: [Form Field - Free Text, Single Line] (rename to Pre-transfusion Platelet Count)
            //    Post Platelet Count 2: [Form Field - Free Text, Single Line] (rename to Post-transfusion Platelet Count)
            //    CCI: [Form Field - Free Text, Single Line]
            array(
                'sectionName' => "CCI (Corrected Count Increment) Instance",
                'sectionObjectTypeName' => "Form Section Array",
                'sectionParentName' => 'CCI (Corrected Count Increment) Calculations',
                'fields' => array(
                    'CCI date'=>'Form Field - Full Date and Time',
                    'CCI Platelet Type Transfused'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","CCIPlateletTypeTransfusedList"),
                    'Pre-transfusion Platelet Count'=>'Form Field - Free Text, Single Line',
                    'Post-transfusion Platelet Count'=>'Form Field - Free Text, Single Line',
                    //'CCI'=>'Form Field - Free Text, Single Line',
                    //TODO: implement and replace for CCI
                    'CCI'=>'Form Field - Free Text, Single Line, Unlocked, Calculated, Stored',
                )
            ),
            //Miscellaneous [Form Section]
            //    Product Currently Receiving: [Form Field - Dropdown Menu]
            //    Product should be receiving: [Form Field - Dropdown Menu] (rename to Product Should Be Receiving)
            //    Product Status: [Form Field - Dropdown Menu]
            //    Expiration Date: [Form Field - Full Date]
            array(
                'sectionName' => "Miscellaneous",
                'fields' => array(
                    'Product Currently Receiving'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","PlateletTransfusionProductReceivingList"),
                    'Product Should Be Receiving'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","PlateletTransfusionProductReceivingList"),
                    'Product Status'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionProductStatusList"),
                    'Expiration Date'=>'Form Field - Full Date',
                    'Date'=>'Form Field - Full Date',
                )
            ),

        );
        $ComplexplateletsummaryForm = $this->addFormToHolder($parent,"Complex platelet summary",$sections);

        ///////////////// Set CCI Unit Platelet Count Default Value: 3 [Free Text Field Default Value] /////////////////
        //$UnitPlateletCount = $this->em->getRepository("OlegUserdirectoryBundle:FormNode")->findOneByName("Unit Platelet Count");
        $mapper = array(
            'prefix' => "Oleg",
            'className' => "FormNode",
            'bundleName' => "UserdirectoryBundle"
        );
        //CCI (Corrected Count Increment) Calculations
        $CCISection = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent("CCI (Corrected Count Increment) Calculations",$ComplexplateletsummaryForm,$mapper);
        if( !$CCISection ) {
            exit('FormNode not found by name "CCI (Corrected Count Increment) Calculations"');
        }
        $UnitPlateletCount = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent("Unit Platelet Count",$CCISection,$mapper);
        if( !$UnitPlateletCount ) {
            exit('FormNode not found by name "Unit Platelet Count"');
        }
        $CCIUnitPlateletCountDefaultValueList = $this->em->getRepository("OlegUserdirectoryBundle:CCIUnitPlateletCountDefaultValueList")->findOneByName("3");
        if( !$CCIUnitPlateletCountDefaultValueList ) {
            exit('CCIUnitPlateletCountDefaultValueList not found by name "3"');
        }
        if( $UnitPlateletCount ) {
            //echo "Unit Platelet Count found=".$UnitPlateletCount->getId()."<br>";
            //echo "0 namespace=".$UnitPlateletCount->getEntityNamespace()." ".$UnitPlateletCount->getEntityName()."<br>";
            $UnitPlateletCount->setObject($CCIUnitPlateletCountDefaultValueList);
            //echo "1 namespace=".$UnitPlateletCount->getEntityNamespace()." ".$UnitPlateletCount->getEntityName()."<br>";
            //$this->em->persist($UnitPlateletCount);
            $this->em->flush($UnitPlateletCount);
        } else {
            exit('Unit Platelet Count setObject not set');
        }
        ///////////////// EOF Set CCI Unit Platelet Count Default Value: 3 [Free Text Field Default Value] /////////////////

        ////////////////////// Transfusion Medicine -> Complex factor summary [Message Category] /////////////////
        // Laboratory Values [Form Section]
        //  Relevant Laboratory Values: [Form Field - Free Text] //replace it by a section called "Relevant Lab Values" of type "From Section Array"
        // Miscellaneous [Form Section]
        //  Product receiving: [Form Field - Free Text, Single Line]
        //  Transfusion Product Status: [Form Field - Dropdown Menu]
//        $sections = array(
//            //replace it by a section called "Relevant Lab Values" of type "From Section Array"
//            //finish: https://bitbucket.org/weillcornellpathology/call-logbook-plan/issues/36/next-steps
//            array(
//                'sectionName' => "Relevant Laboratory Values",
//                'sectionObjectTypeName' => "Form Section Array",
//                'fields' => array(
//                    'Lab Result Name'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","LabResultNameList"),
//                    'Lab Result Value'=>'Form Field - Free Text, Single Line',
//                    'Lab Result Units of Measure'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","LabResultUnitsMeasureList"),
//                    'Lab Result Interpretation'=>'Form Field - Free Text',
//                    'Lab Result Date'=>'Form Field - Full Date and Time',
//                    'Lab Result Flag'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","LabResultFlagList"),
//                    'Lab Result Comment'=>'Form Field - Free Text',
//                    'Lab Result Signatory'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","PathologyResultSignatoriesList"),
//                )
//            ),
//            array(
//                'sectionName' => "Miscellaneous",
//                'fields' => array(
//                    'Product receiving'=>'Form Field - Free Text, Single Line',
//                    'Transfusion Product Status'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionProductStatusList"),
//                )
//            ),
//        );
//        $this->addFormToHolder($parent,"Complex factor summary",$sections);
        $this->createLabValuesSection($parent,"Complex factor summary");

        ///////////////// Transfusion Medicine -> WinRho [Message Category]
        //        Miscellaneous [Form Section]
        //    Weight: [Form Field - Free Text, Single Line]
        //    Dosing: [Form Field - Free Text, Single Line]
        //    IU: [Form Field - Free Text, Single Line]
        $sections = array(
            array(
                'sectionName' => "Miscellaneous",
                'fields' => array(
                    'Weight'=>'Form Field - Free Text, Single Line',
                    'Dosing'=>'Form Field - Free Text, Single Line',
                    'IU'=>'Form Field - Free Text, Single Line',
                )
            ),
        );
        $this->addFormToHolder($parent,"WinRho",$sections);

        //TODO: replace it by a section called "Relevant Lab Values" of type "From Section Array"
        if(1) {
            /////////////////////////////////////        Transfusion Medicine -> Special needs [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Special needs");

            //Transfusion Medicine -> Other [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Other","Transfusion Medicine");

            //Microbiology [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Microbiology");

            //Coagulation [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Coagulation");

            //Hematology [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Hematology");

            //Chemistry [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Chemistry");

            //Cytogenetics [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Cytogenetics");

            //Molecular [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Molecular");

            //Other [Message Category]
            //Laboratory Values [Form Section]
            //    Relevant Laboratory Values: [Form Field - Free Text]
            $this->createLabValuesSection($parent,"Other","Pathology Call Log Entry");
        }



        return $parentForm;
    }

    public function createV2TransfusionMedicine($parent) {

        $objectTypeForm = $this->getObjectTypeByName('Form');
        $objectTypeSection = $this->getObjectTypeByName('Form Section');
        $objectTypeString = $this->getObjectTypeByName('Form Field - Free Text, Single Line');

        //        Transfusion Medicine [Message Category]
        //
        //    Laboratory Values [Form Section]
        //
        //    Hemoglobin: [Form Field - Free Text, Single Line]
        //
        //    Platelets: [Form Field - Free Text, Single Line]

        //Transfusion Medicine (Form)
        $formParams = array(
            'parent' => $parent,
            'name' => "Transfusion Medicine",
            'objectType' => $objectTypeForm,
            //'showLabel' => false,
        );
        $transfusionMedicine = $this->createV2FormNode($formParams);
        $this->setMessageCategoryListLink("Transfusion Medicine",$transfusionMedicine);

        ////////////// Laboratory Values [Form Section] //////////////////
        $formParams = array(
            'parent' => $transfusionMedicine,
            'name' => "Laboratory Values of Interest",
            'objectType' => $objectTypeSection,
            //'showLabel' => true,
        );
        $laboratoryValues = $this->createV2FormNode($formParams);

        //Hemoglobin: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Hemoglobin",
            'objectType' => $objectTypeString,
            //'showLabel' => true,
        );
        $HemoglobinString = $this->createV2FormNode($formParams);

        //Platelets: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Platelets",
            'objectType' => $objectTypeString,
            //'showLabel' => true,
        );
        $PlateletsString = $this->createV2FormNode($formParams);
        ////////////// EOF Laboratory Values [Form Section] //////////////////

        return $transfusionMedicine;
    }

    public function createV2PathologyCallLogEntryFormNode($parent) {

        $objectTypeForm = $this->getObjectTypeByName('Form');
        $objectTypeSection = $this->getObjectTypeByName('Form Section');
        $objectTypeText = $this->getObjectTypeByName('Form Field - Free Text');
        //echo "objectTypeForm=".$objectTypeForm."<br>";

        //$messageCategoryName = "Pathology Call Log Entry";

        //"Pathology Call Log Entry" [Form]
        $formParams = array(
            'parent' => $parent,
            'name' => "Pathology Call Log Entry",
            'objectType' => $objectTypeForm,
        );
        $PathologyCallLogEntryFom = $this->createV2FormNode($formParams); //$formNode
        $this->setMessageCategoryListLink("Pathology Call Log Entry",$PathologyCallLogEntryFom);

        //History/Findings (Section)
        $formParams = array(
            'parent' => $PathologyCallLogEntryFom,
            'name' => "History/Findings",
            'objectType' => $objectTypeSection,
        );
        $historySection = $this->createV2FormNode($formParams);

        //History/Findings Text
        $formParams = array(
            'parent' => $historySection,
            'name' => "History/Findings Text",
            'objectType' => $objectTypeText,
            'showLabel' => false,
        );
        $historyText = $this->createV2FormNode($formParams);

        //Impression/Outcome (Section)
        $formParams = array(
            'parent' => $PathologyCallLogEntryFom,
            'name' => "Impression/Outcome",
            'objectType' => $objectTypeSection,
        );
        $impressionSection = $this->createV2FormNode($formParams);

        //Impression/Outcome Text
        $formParams = array(
            'parent' => $impressionSection,
            'name' => "Impression/Outcome Text",
            'objectType' => $objectTypeText,
            'showLabel' => false,
        );
        $impressionText = $this->createV2FormNode($formParams);


        return $PathologyCallLogEntryFom;
    }

    //"Relevant Lab Values" of type "From Section Array"
    //https://bitbucket.org/weillcornellpathology/call-logbook-plan/issues/36/next-steps
    //$holderName: i.e. "Complex factor summary"
    public function createLabValuesSection($parent,$holderName,$parentMessageCategoryName=null) {
        $sections = array(
            array(
                'sectionName' => "Relevant Laboratory Values",
                'sectionObjectTypeName' => "Form Section Array",
                'fields' => array(
                    'Lab Result Name'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","LabResultNameList"),
                    'Lab Result Value'=>'Form Field - Free Text, Single Line',
                    'Lab Result Units of Measure'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","LabResultUnitsMeasureList"),
                    'Lab Result Interpretation'=>'Form Field - Free Text',
                    'Lab Result Date'=>'Form Field - Full Date and Time',
                    'Lab Result Flag'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","LabResultFlagList"),
                    'Lab Result Comment'=>'Form Field - Free Text',
                    'Lab Result Signatory'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","PathologyResultSignatoriesList"),
                )
            ),
            array(
                'sectionName' => "Miscellaneous",
                'fields' => array(
                    'Product receiving'=>'Form Field - Free Text, Single Line',
                    'Transfusion Product Status'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionProductStatusList"),
                )
            ),
        );
        $this->addFormToHolder($parent,$holderName,$sections,$parentMessageCategoryName);
    }


    //run by: /list/generate-test-form-node-tree/
    public function createTestFormNodes()
    {

        //1) create "Test" form section
//        $sections = array(
//            array(
//                'sectionName' => "Test",
//            ),
//        );
//        $testFormSection = $this->addFormToHolder($parent,"Test",$sections);

        $parentNode = $this->em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName('Pathology Call Log Book');

        //Test Section 1 (Form Section)
        //Test Field 01: Form Field - Free Text, Single Line
        //Test Field 02: Form Field - Free Text
        //Test Field 03: Form Field - Free Text, RTF
        //Test Field 04: Form Field - Free Text, HTML
        //Test Field 05: Form Field - Full Date
        //Test Field 06: Form Field - Time
        //Test Field 07: Form Field - Full Date and Time
        //Test Field 08: Form Field - Year
        //Test Section 2 (Form Section)
        //Test Field 09: Form Field - Month
        //Test Field 10: Form Field - Date
        //Test Field 11: Form Field - Day of the Week
        //Test Field 12: Form Field - Dropdown Menu (you can link this to show any real lists you have)
        //Test Field 13: Form Field - Checkbox
        //Test Field 14: Form Field - Radio Button
        //Test Field 15: Form Field - Dropdown Menu - Allow Multiple Selections (you can link this to show any real lists you have)
        //Test Field 16: Form Field - Dropdown Menu (linking to the "Complex patient list" which in turn has items on it marked as "Linked Object - Patient") thus this dropdown menu should show patients currently on the "Complex Patient List".) If we add a second patient list, changing this dropdown menu to list patients from the second list should be as easy as changing the ID in the "Link to List ID" column.
        $sections = array(
            array(
                'sectionName' => "Test Section 1",
                'fields' => array(
                    'Test Field 01'=>'Form Field - Free Text, Single Line',
                    'Test Field 02'=>'Form Field - Free Text',
                    'Test Field 03'=>'Form Field - Free Text, RTF',
                    'Test Field 04'=>'Form Field - Free Text, HTML',
                    'Test Field 05'=>'Form Field - Full Date',
                    'Test Field 06'=>'Form Field - Time',
                    'Test Field 07'=>'Form Field - Full Date and Time',
                    'Test Field 08'=>'Form Field - Year',
                )
            ),
            array(
                'sectionName' => "Test Section 2",
                'sectionObjectTypeName' => "Form Section Array",
                'fields' => array(
                    'Test Field 09'=>array('Form Field - Month',"Oleg\\UserdirectoryBundle\\Entity","MonthsList"), //'Form Field - Month',
                    'Test Field 10'=>'Form Field - Date',
                    'Test Field 11'=>array('Form Field - Day of the Week',"Oleg\\UserdirectoryBundle\\Entity","WeekDaysList"),  //'Form Field - Day of the Week',
                    'Test Field 12'=>array('Form Field - Dropdown Menu',"Oleg\\UserdirectoryBundle\\Entity","TransfusionProductStatusList"),
                    'Test Field 13'=>array('Form Field - Checkbox',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Test Field 14'=>array('Form Field - Radio Button',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Test Field 15'=>array('Form Field - Dropdown Menu - Allow Multiple Selections',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Test Field 16'=>array('Form Field - Dropdown Menu',"Oleg\\CallLogBundle\\Entity","PathologyCallComplexPatients"),
                    'Test Field 17'=>array('Form Field - Checkboxes',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                ),
            ),
            array(
                'sectionName' => "Test Section 3",
                'fields' => array(
                    'Test Field 18'=>'Form Field - Time, with Time Zone',
                    'Test Field 19'=>'Form Field - Full Date and Time, with Time Zone',
                    'Test Field 20'=>array('Form Field - Dropdown Menu - Allow New Entries',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Test Field 21'=>array('Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries',"Oleg\\UserdirectoryBundle\\Entity","BloodTypeList"),
                    'Test Field 22'=>'Form Field - Free Text, Single Line, Numeric, Unsigned Positive Integer',
                    'Test Field 23'=>'Form Field - Free Text, Single Line, Numeric, Signed Integer',
                    'Test Field 24'=>'Form Field - Free Text, Single Line, Numeric, Signed Float',
                    'Test Field 25'=>'Form Field - Free Text, Single Line, Locked, Calculated, Stored',
                    'Test Field 26'=>'Form Field - Free Text, Single Line, Unlocked, Calculated, Stored',
                    'Test Field 27'=>'Form Field - Free Text, Single Line, Locked, Calculated, Visual Aid',
                    'Test Field 28'=>array('Form Field - Dropdown Menu - Allow New Entries',"Oleg\\UserdirectoryBundle\\Entity","PathologyResultSignatoriesList"),
                    'Test Field 29'=>array('Form Field - Dropdown Menu - Allow Multiple Selections - Allow New Entries',"Oleg\\UserdirectoryBundle\\Entity","PathologyResultSignatoriesList"),
                ),
            ),

        );
        $this->addFormToHolder($parentNode,"Test",$sections);


    }































    ///////////////////// OLD ///////////////////////////////
    //Create a "Test" form containing every element of object type that begins with "Form Field - ...", then create a new "Test" Message type ("Service" level)
    //run by link: order/directory/list/generate-test-form-node-tree/
    public function generateTestFormNode() {

        return;

        $em = $this->em;
        $username = $this->container->get('security.context')->getToken()->getUser();

        //root
        $categories = array(
            'Root Form' => array(),
        );
        $count = 10;
        $level = 0;
        $count = $this->addNestedsetNodeRecursevely(null,$categories,$level,$username,$count);
        $rootNode = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName('Root Form');
        //echo "rootNode=".$rootNode."<br>";


        $objectTypeForm = $this->getObjectTypeByName('Form');
        $objectTypeSection = $this->getObjectTypeByName('Form Section');
        $objectTypeText = $this->getObjectTypeByName('Form Field - Free Text');
        $objectTypeString = $this->getObjectTypeByName('Form Field - Free Text, Single Line');
        //$objectTypeDropdown = $this->getObjectTypeByName('Form Field - Dropdown Menu');
        //$objectTypeDropdownValue = $this->getObjectTypeByName('Dropdown Menu Value');
        //$objectTypeDate = $this->getObjectTypeByName('Form Field - Date');
        //$objectTypeFullDate = $this->getObjectTypeByName('Form Field - Full Date');
        //$objectTypeFullDateTime = $this->getObjectTypeByName('Form Field - Full Date and Time');



        //////////////////// Test //////////////////////
        $messageTestService = "Test";

        $formParams = array(
            'parent' => $rootNode,
            'name' => $messageTestService,
            'objectType' => $objectTypeForm,
            'showLabel' => false,
            'visible' => false
        );
        $TestForm = $this->createFormNode($formParams);
        //$this->setFormNodeToMessageCategory($messageTestService,array($TestForm));

        ///////////////////////////// Section 1 /////////////////////////////////
        //Test Section 1 (Form Section)
        //Transfusion Reaction Workup [Form Section]
        $formParams = array(
            'parent' => $TestForm,
            'name' => "Test Section 1",
            'placeholder' => "",
            'objectType' => $objectTypeSection,
            'showLabel' => false,
            'visible' => true
        );
        $formTestSection1 = $this->createFormNode($formParams);

        //Test Field 01: Form Field - Free Text, Single Line
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 01 (Form Field - Free Text, Single Line)",
            'placeholder' => "Test Field 01 (Form Field - Free Text, Single Line)",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 02: Form Field - Free Text
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 02 (Form Field - Free Text)",
            'placeholder' => "Test Field 02 (Form Field - Free Text)",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 03: Form Field - Free Text, RTF
        $objectTypeTextRTF = $this->getObjectTypeByName('Form Field - Free Text, RTF');
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 03 (Form Field - Free Text, RTF)",
            'placeholder' => "Test Field 03 (Form Field - Free Text, RTF)",
            'objectType' => $objectTypeTextRTF,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 04: Form Field - Free Text, HTML
        $objectTypeTextHTML = $this->getObjectTypeByName('Form Field - Free Text, HTML');
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 04 (Form Field - Free Text, HTML)",
            'placeholder' => "Test Field 04 (Form Field - Free Text, HTML)",
            'objectType' => $objectTypeTextHTML,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 05: Form Field - Full Date
        $objectType = $this->getObjectTypeByName('Form Field - Full Date');
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 05 (Form Field - Full Date)",
            'placeholder' => "Test Field 05 (Form Field - Full Date)",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 06: Form Field - Time
        $objectType = $this->getObjectTypeByName('Form Field - Time');
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 06: Form Field - Time",
            'placeholder' => "Test Field 06: Form Field - Time",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 07: Form Field - Full Date and Time
        $objectType = $this->getObjectTypeByName('Form Field - Full Date and Time');
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 07: Form Field - Full Date and Time",
            'placeholder' => "Test Field 07: Form Field - Full Date and Time",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 08: Form Field - Year
        $objectType = $this->getObjectTypeByName('Form Field - Year');
        $formParams = array(
            'parent' => $formTestSection1,
            'name' => "Test Field 08: Form Field - Year",
            'placeholder' => "Test Field 08: Form Field - Year",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        ///////////////////////////// Section 2 /////////////////////////////////
        //Test Section 2 (Form Section) //change from "Form Section" to "Form Section Array"
        $objectSectionArrayType = $this->getObjectTypeByName('Form Section Array');
        $formParams = array(
            'parent' => $TestForm,
            'name' => "Test Section 2",
            'placeholder' => "",
            'objectType' => $objectSectionArrayType,
            'showLabel' => false,
            'visible' => true
        );
        $formTestSection2 = $this->createFormNode($formParams);

        //Test Field 09: Form Field - Month
        $objectType = $this->getObjectTypeByName('Form Field - Month');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 09: Form Field - Month",
            'placeholder' => "Test Field 09: Form Field - Month",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "MonthsList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 10: Form Field - Date
        $objectType = $this->getObjectTypeByName('Form Field - Date');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 10: Form Field - Date",
            'placeholder' => "Test Field 10: Form Field - Date",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 11: Form Field - Day of the Week
        $objectType = $this->getObjectTypeByName('Form Field - Day of the Week');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 11: Form Field - Day of the Week",
            'placeholder' => "Test Field 11: Form Field - Day of the Week",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "WeekDaysList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 12: Form Field - Dropdown Menu (you can link this to show any real lists you have)
        $objectType = $this->getObjectTypeByName('Form Field - Dropdown Menu');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 12: Form Field - Dropdown Menu",
            'placeholder' => "Test Field 12: Form Field - Dropdown Menu",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 13: Form Field - Checkbox
        $objectType = $this->getObjectTypeByName('Form Field - Checkbox');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 13: Form Field - Checkbox",
            'placeholder' => "Test Field 13: Form Field - Checkbox",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true,
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 14: Form Field - Radio Button
        $objectType = $this->getObjectTypeByName('Form Field - Radio Button');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 14: Form Field - Radio Button",
            'placeholder' => "Test Field 14: Form Field - Radio Button",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 15: Form Field - Dropdown Menu - Allow Multiple Selections (you can link this to show any real lists you have)
        $objectType = $this->getObjectTypeByName('Form Field - Dropdown Menu - Allow Multiple Selections');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 15: Form Field - Dropdown Menu - Allow Multiple Selections",
            'placeholder' => "Test Field 15: Form Field - Dropdown Menu - Allow Multiple Selections",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        //Test Field 16: Form Field - Dropdown Menu
        // (linking to the "Complex patient list" which in turn has items on it marked as "Linked Object - Patient")
        // thus this dropdown menu should show patients currently on the "Complex Patient List".)
        // If we add a second patient list, changing this dropdown menu to list patients from the second list should be
        // as easy as changing the ID in the "Link to List ID" column.
        $objectType = $this->getObjectTypeByName('Form Field - Dropdown Menu');
        $formParams = array(
            'parent' => $formTestSection2,
            'name' => "Test Field 16: Form Field - Dropdown Menu (Pathology Call Complex Patients)",
            'placeholder' => "Test Field 16: Form Field - Dropdown Menu (Pathology Call Complex Patients)",
            'objectType' => $objectType,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\CallLogBundle\\Entity",
            'className' => "PathologyCallComplexPatients"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory($messageTestService,array($formNode));

        return round($count/10);
    }

    public function createPathologyCallLogEntryFormNode($parent) {

        $objectTypeForm = $this->getObjectTypeByName('Form');
        $objectTypeSection = $this->getObjectTypeByName('Form Section');
        $objectTypeText = $this->getObjectTypeByName('Form Field - Free Text');
        //echo "objectTypeForm=".$objectTypeForm."<br>";

        $messageCategoryName = "Pathology Call Log Entry";

        $formParams = array(
            'parent' => $parent,
            'name' => $messageCategoryName,
            'objectType' => $objectTypeForm,
            'showLabel' => false,
            'visible' => false
        );
        $PathologyCallLogEntry = $this->createFormNode($formParams);

        //History/Findings (Section)
        $formParams = array(
            'parent' => $PathologyCallLogEntry,
            'name' => "History/Findings",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $historySection = $this->createFormNode($formParams);

        //History/Findings Text
        $formParams = array(
            'parent' => $historySection,
            'name' => "History/Findings Text",
            'placeholder' => "History/Findings Text",
            'objectType' => $objectTypeText,
            'showLabel' => false,
            'visible' => true
        );
        $historyText = $this->createFormNode($formParams);

        //Impression/Outcome (Section)
        $formParams = array(
            'parent' => $PathologyCallLogEntry,
            'name' => "Impression/Outcome",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $impressionSection = $this->createFormNode($formParams);

        //Impression/Outcome Text
        $formParams = array(
            'parent' => $impressionSection,
            'name' => "Impression/Outcome Text",
            'placeholder' => "Impression/Outcome Text",
            'objectType' => $objectTypeText,
            'showLabel' => false,
            'visible' => true
        );
        $impressionText = $this->createFormNode($formParams);

        //attach this formnode to the MessageCategory "Transfusion Medicine"
        $this->setFormNodeToMessageCategory($messageCategoryName,array($historyText,$impressionText));

        return $PathologyCallLogEntry;
    }

    public function createTransfusionMedicine($parent) {

        $objectTypeForm = $this->getObjectTypeByName('Form');
        $objectTypeSection = $this->getObjectTypeByName('Form Section');
        //$objectTypeFieldGroup = $this->getObjectTypeByName('Field Group');
        $objectTypeText = $this->getObjectTypeByName('Form Field - Free Text');
        $objectTypeString = $this->getObjectTypeByName('Form Field - Free Text, Single Line');
        $objectTypeDropdown = $this->getObjectTypeByName('Form Field - Dropdown Menu');
        $objectTypeDropdownValue = $this->getObjectTypeByName('Dropdown Menu Value');
        $objectTypeDate = $this->getObjectTypeByName('Form Field - Date');
        $objectTypeFullDate = $this->getObjectTypeByName('Form Field - Full Date');
        $objectTypeFullDateTime = $this->getObjectTypeByName('Form Field - Full Date and Time');

        $messageCategoryName = "Transfusion Medicine";

        //Transfusion Medicine (Form)
        $formParams = array(
            'parent' => $parent,
            'name' => $messageCategoryName,
            'objectType' => $objectTypeForm,
            'showLabel' => false,
            'visible' => false
        );
        $transfusionMedicine = $this->createFormNode($formParams);

        ////////////// Laboratory Values [Form Section] //////////////////
        $formParams = array(
            'parent' => $transfusionMedicine,
            'name' => "Laboratory Values of Interest",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $laboratoryValues = $this->createFormNode($formParams);

        //Hemoglobin: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Hemoglobin",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $HemoglobinString = $this->createFormNode($formParams);

        //Platelets: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Platelets",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PlateletsString = $this->createFormNode($formParams);
        ////////////// EOF Laboratory Values [Form Section] //////////////////

        //attach this formnode to the MessageCategory "Transfusion Medicine"
        $this->setFormNodeToMessageCategory($messageCategoryName,array($HemoglobinString,$PlateletsString));


        //////////////////////////////////////////////////////
        //////// Transfusion Medicine -> First dose plasma [Message Category]
        //$formSectionArr = array();
        $messageCategoryName = "First dose plasma";

        //INR: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "INR",
            'placeholder' => "INR",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        //$formSectionArr[] = $formParams;
        $INRString = $this->createFormNode($formParams);

        //PT: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "PT",
            'placeholder' => "PT",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        //$formSectionArr[] = $formParams;
        $PTString = $this->createFormNode($formParams);

        //PTT: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "PTT",
            'placeholder' => "PTT",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        //$formSectionArr[] = $formParams;
        $PTTString = $this->createFormNode($formParams);

        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory($messageCategoryName,array($INRString,$PTString,$PTTString));

        //////////////////////////////////////////////////////
        //Transfusion Medicine -> First dose platelets [Message Category]
        $messageCategoryName = "First dose platelets";

        //Miscellaneous [Form Section]
        $formParams = array(
            'parent' => $transfusionMedicine,
            'name' => "Miscellaneous",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $miscellaneous = $this->createFormNode($formParams);

        //Medication: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Medication",
            'placeholder' => "Medication",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $MedicationString = $this->createFormNode($formParams);

        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory($messageCategoryName,array($MedicationString));


        //////////////////////////////////////////////////////
        //Transfusion Medicine -> Third+ dose platelets [Message Category]
        $messageCategoryName = "Third+ dose platelets";

        //Laboratory Values [Form Section]
        //CCI: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "CCI",
            'placeholder' => "CCI",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $CCIString = $this->createFormNode($formParams);

        //Miscellaneous [Form Section]
        //Platelet Goal: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Platelet Goal",
            'placeholder' => "Platelet Goal",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PlateletGoalString = $this->createFormNode($formParams);

        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory($messageCategoryName,array($CCIString,$PlateletGoalString));


        //////////////////////////////////////////////////////
        //Transfusion Medicine -> Cryoprecipitate [Message Category]
        $messageCategoryName = "Cryoprecipitate";
        //Laboratory Values [Form Section]
        //INR: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "INR",
            'placeholder' => "INR",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $INRString = $this->createFormNode($formParams);

        //PT: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "PT",
            'placeholder' => "PT",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PTString = $this->createFormNode($formParams);

        //PTT: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "PTT",
            'placeholder' => "PTT",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PTTString = $this->createFormNode($formParams);

        //Fibrinogen: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Fibrinogen",
            'placeholder' => "Fibrinogen",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $FibrinogenString = $this->createFormNode($formParams);

        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory($messageCategoryName,array($INRString,$PTString,$PTTString,$FibrinogenString));


        //////////////////////////////////////////////////////
        //Transfusion Medicine -> MTP [Message Category]
        $messageCategoryName = "MTP";
        //Laboratory Values [Form Section]
        //INR: [Form Field - Free Text, Single Line]
        //PT: [Form Field - Free Text, Single Line]
        //PTT: [Form Field - Free Text, Single Line]
        //Fibrinogen: [Form Field - Free Text, Single Line]
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory($messageCategoryName,array($INRString,$PTString,$PTTString,$FibrinogenString));

        //////////////////////////////////////////////////////
        //Transfusion Medicine -> Emergency release [Message Category]
        //Miscellaneous [Form Section]
        //Blood Type of Unit: [Form Field - Free Text, Single Line]
        //change to [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Blood type of Unit",
            'placeholder' => "Blood type of Unit",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Emergency release",array($formNode));
        //Blood Type of Patient: [Form Field - Free Text, Single Line]
        //change to [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Blood Type of Patient",
            'placeholder' => "Blood Type of Patient",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Emergency release",array($formNode));

        //////////////////////////////////////////////////////
        //Transfusion Medicine -> Payson transfusion [Message Category]
        //Empty
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Payson transfusion",array());

        //Transfusion Medicine -> Incompatible crossmatch [Message Category]
        //Miscellaneous [Form Section]
        //Blood Type of Unit: [Form Field - Free Text, Single Line]
        //change to [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Blood type of Unit",
            'placeholder' => "Blood type of Unit",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $BloodtypeofunitDropdowm = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Incompatible crossmatch",array($BloodtypeofunitDropdowm));
        //Blood Type of Patient: [Form Field - Free Text, Single Line]
        //change to [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Blood Type of Patient",
            'placeholder' => "Blood Type of Patient",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $BloodtypeofunitDropdowm = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Incompatible crossmatch",array($BloodtypeofunitDropdowm));
        //Antibodies: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Antibodies",
            'placeholder' => "Antibodies",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $AntibodiesString = $this->createFormNode($formParams);
        //Phenotype: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Phenotype",
            'placeholder' => "Phenotype",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PhenotypeString = $this->createFormNode($formParams);
        //Incompatibility: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Incompatibility",
            'placeholder' => "Incompatibility",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $IncompatibilityString = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Incompatible crossmatch",array($AntibodiesString,$PhenotypeString,$IncompatibilityString));


        //////////////////////////////////////////////////////
        //Transfusion Medicine -> Transfusion reaction [Message Category]
        //Miscellaneous [Form Section]
        //Blood Product Transfused [Dropdown Menu Value List]
        //$objectTypeDropdown = $this->getObjectTypeByName('Form Field - Dropdown Menu'); //,'ObjectTypeBloodProductTransfused');
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Blood Product Transfused",
            'placeholder' => "Blood Product Transfused",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodProductTransfusedList"
        );
        $BloodProductTransfused = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($BloodProductTransfused));

        //Transfusion Reaction Type [Dropdown Menu Value List]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Transfusion Reaction Type",
            'placeholder' => "Transfusion Reaction Type",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionReactionTypeList"
        );
        $TransfusionReactionType = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($TransfusionReactionType));

        //        Vitals [Form Section]
        $formParams = array(
            'parent' => $transfusionMedicine,
            'name' => "Vitals",
            'placeholder' => "",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $VitalsSection = $this->createFormNode($formParams);
        $VitalsArr = array();
        //    Pre-Temp: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Pre-Temp",
            'placeholder' => "Pre-Temp",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PreTemp = $this->createFormNode($formParams);
        $VitalsArr[] = $PreTemp;
        //    Pre-HR: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Pre-HR",
            'placeholder' => "Pre-HR",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PreHR = $this->createFormNode($formParams);
        $VitalsArr[] = $PreHR;
        //    Pre-RR: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Pre-RR",
            'placeholder' => "Pre-RR",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PreRR = $this->createFormNode($formParams);
        $VitalsArr[] = $PreRR;
        //    Pre-O2 sat: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Pre-O2",
            'placeholder' => "Pre-O2",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PreO2 = $this->createFormNode($formParams);
        $VitalsArr[] = $PreO2;
        //    Pre-BP: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Pre-BP",
            'placeholder' => "Pre-BP",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PreBP = $this->createFormNode($formParams);
        $VitalsArr[] = $PreBP;

        //    Post-Temp: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Post-Temp",
            'placeholder' => "Post-Temp",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PostTemp = $this->createFormNode($formParams);
        $VitalsArr[] = $PostTemp;
        //    Post-HR: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Post-HR",
            'placeholder' => "Post-HR",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PostHR = $this->createFormNode($formParams);
        $VitalsArr[] = $PostHR;
        //    Post-RR: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Post-RR",
            'placeholder' => "Post-RR",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PostRR = $this->createFormNode($formParams);
        $VitalsArr[] = $PostRR;
        //    Post-O2 sat: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Post-O2",
            'placeholder' => "Post-O2",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PostO2 = $this->createFormNode($formParams);
        $VitalsArr[] = $PostO2;
        //    Post-BP: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $VitalsSection,
            'name' => "Post-BP",
            'placeholder' => "Post-BP",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $PostBP = $this->createFormNode($formParams);
        $VitalsArr[] = $PostBP;
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",$VitalsArr);

        //Transfusion Reaction Workup [Form Section]
        $formParams = array(
            'parent' => $transfusionMedicine,
            'name' => "Transfusion Reaction Workup",
            'placeholder' => "",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $TransfusionReactionWorkupSection = $this->createFormNode($formParams);
        $TransfusionReactionWorkupSectionArr = array();
        //    Transfusion Reaction Workup Description [Form Field - Free Text]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Transfusion Reaction Workup Description",
            'placeholder' => "Transfusion Reaction Workup Description",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $TransfusionReactionWorkupDescription = $this->createFormNode($formParams);
        $TransfusionReactionWorkupSectionArr[] = $TransfusionReactionWorkupDescription;

        //    Clerical error: [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Clerical error",
            'placeholder' => "Clerical error",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            //'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            //'className' => "ClericalErrorList"
        );
        $ClericalerrorDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($ClericalerrorDropdowm));
        //        Transfusion Reaction Clerical Error Type [Dropdown Menu Value List]
        //            Yes [Dropdown Menu Value]
        $formParams = array(
            'parent' => $ClericalerrorDropdowm,
            'name' => "Yes",
            'placeholder' => "Yes",
            'objectType' => $objectTypeDropdownValue,
            'showLabel' => true,
            'visible' => true,
        );
        $ClericalerrorDropdowmYes = $this->createFormNode($formParams);
        //            None [Dropdown Menu Value]
        $formParams = array(
            'parent' => $ClericalerrorDropdowm,
            'name' => "None",
            'placeholder' => "None",
            'objectType' => $objectTypeDropdownValue,
            'showLabel' => true,
            'visible' => true,
        );
        $ClericalerrorDropdowmNone = $this->createFormNode($formParams);

        //    Blood type of unit: [Form Field - Dropdown Menu]
        //        Blood Types [Dropdown Menu Value List]
        //            A+ [Dropdown Menu Value]
        //            A- [Dropdown Menu Value]
        //            B+ [Dropdown Menu Value]
        //            B- [Dropdown Menu Value]
        //            O+ [Dropdown Menu Value]
        //            O- [Dropdown Menu Value]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Blood type of Unit",
            'placeholder' => "Blood type of Unit",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $BloodtypeofunitDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($BloodtypeofunitDropdowm));

        //    Blood type of pre-transfusion specimen: [Form Field - Dropdown Menu]
        //        Blood Types [Dropdown Menu Value List] SAME LIST AS ABOVE, DO NOT DUPLICATE, just link to it via Link to List ID
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Blood type of pre-transfusion specimen",
            'placeholder' => "Blood type of pre-transfusion specimen",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $BloodtypeofunitSpecimenDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($BloodtypeofunitSpecimenDropdowm));

        //    Blood type of post-transfusion specimen: [Form Field - Dropdown Menu]
        //        Blood Types [Dropdown Menu Value List] SAME LIST AS ABOVE, DO NOT DUPLICATE, just link to it via Link to List ID
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Blood type of post-transfusion specimen",
            'placeholder' => "Blood type of post-transfusion specimen",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "BloodTypeList"
        );
        $BloodtypeofunitPostTransfusionSpecimenDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($BloodtypeofunitPostTransfusionSpecimenDropdowm));

        //    Pre-transfusion antibody screen: [Form Field - Dropdown Menu]
        //        Transfusion antibody screen results [Dropdown Menu Value List]
        //            Positive [Dropdown Menu Value]
        //            Negative [Dropdown Menu Value]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Pre-transfusion antibody screen",
            'placeholder' => "Pre-transfusion antibody screen",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionAntibodyScreenResultsList"
        );
        $PretransfusionAntibodyScreenDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($PretransfusionAntibodyScreenDropdowm));

        //    Post-transfusion antibody screen: [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Post-transfusion antibody screen",
            'placeholder' => "Post-transfusion antibody screen",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionAntibodyScreenResultsList"
        );
        $PosttransfusionAntibodyScreenDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($PosttransfusionAntibodyScreenDropdowm));

        //    Pre-transfusion DAT: [Form Field - Dropdown Menu]
        //        Transfusion DAT results [Dropdown Menu Value List]
        //            Positive [Dropdown Menu Value]
        //            Negative [Dropdown Menu Value]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Pre-transfusion DAT",
            'placeholder' => "Pre-transfusion DAT",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionDATResultsList"
        );
        $TransfusionDATDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($TransfusionDATDropdowm));

        //    Post-transfusion DAT: [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Post-transfusion DAT",
            'placeholder' => "Post-transfusion DAT",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionDATResultsList"
        );
        $PostTransfusionDATDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($PostTransfusionDATDropdowm));

        //    Pre-transfusion crossmatch: [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Pre-transfusion crossmatch",
            'placeholder' => "Pre-transfusion crossmatch",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionCrossmatchResultsList"
        );
        $PreTransfusionCrossmatchDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($PreTransfusionCrossmatchDropdowm));

        //    Post-transfusion crossmatch: [Form Field - Dropdown Menu]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Post-transfusion crossmatch",
            'placeholder' => "Post-transfusion crossmatch",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionCrossmatchResultsList"
        );
        $PostTransfusionCrossmatchDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($PostTransfusionCrossmatchDropdowm));

        //    Pre-transfusion hemolysis check: [Form Field - Dropdown Menu]
        //        Transfusion hemolysis check results [Dropdown Menu Value List]
        //            Hemolysis [Dropdown Menu Value]
        //            No hemolysis [Dropdown Menu Value]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Pre-transfusion hemolysis check",
            'placeholder' => "Pre-transfusion hemolysis check",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionHemolysisCheckResultsList"
        );
        $PreTransfusionHemolysisCheckDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($PreTransfusionHemolysisCheckDropdowm));

        //    Post-transfusion hemolysis check: [Form Field - Dropdown Menu]
        //        Transfusion hemolysis check results [Dropdown Menu Value List] SAME LIST AS ABOVE, DO NOT DUPLICATE, just link to it via Link to List ID
        //            Hemolysis [Dropdown Menu Value]
        //            No hemolysis [Dropdown Menu Value]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Post-transfusion hemolysis check",
            'placeholder' => "Post-transfusion hemolysis check",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionHemolysisCheckResultsList"
        );
        $PostTransfusionHemolysisCheckDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($PostTransfusionHemolysisCheckDropdowm));

        //    Microbiology: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $TransfusionReactionWorkupSection,
            'name' => "Microbiology",
            'placeholder' => "Microbiology",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $Microbiology = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Transfusion reaction",array($Microbiology));


        //Transfusion Medicine -> Complex platelet summary [Message Category]
        //Laboratory Values [Form Section]
        //    HLA A: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "HLA A",
            'placeholder' => "HLA A",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));
        //    HLA B: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "HLA B",
            'placeholder' => "HLA B",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));
        //    Rogosin PRA: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Rogosin PRA",
            'placeholder' => "Rogosin PRA",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));
        //    Rogosin date: [Form Field - Full Date]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Rogosin date",
            'placeholder' => "Rogosin date",
            'objectType' => $objectTypeFullDate,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //    Antibodies [Form Field - Dropdown Menu]
        //        Complex platelet summary antibodies [Dropdown Menu Value List]
        //            HLA [Dropdown Menu Value]
        //            HPA [Dropdown Menu Value]
        //            None [Dropdown Menu Value]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Antibodies",
            'placeholder' => "Antibodies",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "ComplexPlateletSummaryAntibodiesList"
        );
        $PostTransfusionHemolysisCheckDropdowm = $this->createFormNode($formParams);
        //attach this formnodes to the MessageCategory
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($PostTransfusionHemolysisCheckDropdowm));

        //    NYBC date: [Form Field - Full Date]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "NYBC date",
            'placeholder' => "NYBC date",
            'objectType' => $objectTypeFullDate,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));


        //        CCI (Corrected Count Increment) Calculations: [Form Section]
        $formParams = array(
            'parent' => $transfusionMedicine,
            'name' => "CCI (Corrected Count Increment) Calculations",
            'placeholder' => "",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $CCISection = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($CCISection));

        //    BSA: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $CCISection,
            'name' => "BSA",
            'placeholder' => "BSA",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //    Unit Platelet Count [Form Field - Free Text, Single Line] : USE "Link To List ID" to link to a new list titled "CCI Unit Platelet Count Default Value" with one list item with "3" in the name column, load "3" via this link into this field on load. This mechanism will allow multiple possible default values for a given field depending on rules (once rules are implemented, until then your logic should grab the first value on the list).
        //        CCI Unit Platelet Count Default Value [Free Text Field Default Value List]
        //            3 [Free Text Field Default Value]
        $CCIUnitPlateletCountDefaultValueList = $this->em->getRepository("OlegUserdirectoryBundle:CCIUnitPlateletCountDefaultValueList")->findOneByName("3");
        $formParams = array(
            'parent' => $CCISection,
            'name' => "Unit Platelet Count",
            'placeholder' => "Unit Platelet Count",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true,
            'classObject' => $CCIUnitPlateletCountDefaultValueList
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //CCI (Corrected Count Increment) Instance: [Form Section] NESTED IN "CCI (Corrected Count Increment) Calculations: [Form Section]"
        $formParams = array(
            'parent' => $CCISection,
            'name' => "CCI (Corrected Count Increment) Instance",
            'placeholder' => "",
            'objectType' => $objectTypeSection,
            'showLabel' => true,
            'visible' => true
        );
        $CCIInstanceSection = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($CCIInstanceSection));
        //    CCI date: [Form Field - Full Date and Time]
        $formParams = array(
            'parent' => $CCIInstanceSection,
            'name' => "CCI date",
            'placeholder' => "CCI date",
            'objectType' => $objectTypeFullDateTime,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //    CCI Platelet Type Transfused [Form Field - Dropdown Menu]
        //        CCI Platelet Type Transfused [Dropdown Menu Value List]
        //            Regular Platelets [Dropdown Menu Value]
        //            Crossmatched [Dropdown Menu Value]
        //            HLA matched [Dropdown Menu Value]
        //            ABO matched [Dropdown Menu Value]
        $formParams = array(
            'parent' => $CCIInstanceSection,
            'name' => "CCI Platelet Type Transfused",
            'placeholder' => "CCI Platelet Type Transfused",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "CCIPlateletTypeTransfusedList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //    Pre Platelet Count 1: [Form Field - Free Text, Single Line]
        //rename to Pre-transfusion Platelet Count:
        $formParams = array(
            'parent' => $CCIInstanceSection,
            'name' => "Pre-transfusion Platelet Count",
            'placeholder' => "Pre-transfusion Platelet Count",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));
        //    Post Platelet Count 2: [Form Field - Free Text, Single Line]
        //rename to Post-transfusion Platelet Count
        $formParams = array(
            'parent' => $CCIInstanceSection,
            'name' => "Post-transfusion Platelet Count",
            'placeholder' => "Post-transfusion Platelet Count",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));
        //    CCI: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $CCIInstanceSection,
            'name' => "CCI",
            'placeholder' => "CCI",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));


        //        Miscellaneous [Form Section]
        //    Product Currently Receiving: [Form Field - Dropdown Menu]
        //        Platelet Transfusion Product Receiving [Dropdown Menu Value List]
        //            HLA Platelets [Dropdown Menu Value]
        //            XM Platelets [Dropdown Menu Value]
        //            Regular Platelets [Dropdown Menu Value]
        //            Platelet Drip [Dropdown Menu Value]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Product Currently Receiving",
            'placeholder' => "Product Currently Receiving",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "PlateletTransfusionProductReceivingList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //    Product should be receiving: [Form Field - Dropdown Menu]
        //        Platelet Transfusion Product Receiving [Dropdown Menu Value List] SAME LIST AS ABOVE, DO NOT DUPLICATE, just link to it via Link to List ID
        //rename to Product Should Be Receiving
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Product Should Be Receiving",
            'placeholder' => "Product Should Be Receiving",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "PlateletTransfusionProductReceivingList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //    Product Status: [Form Field - Dropdown Menu]
        //        Transfusion Product Status [Dropdown Menu Value List]
        //            Ordered [Dropdown Menu Value]
        //            Not Ordered [Dropdown Menu Value]
        //            Pending [Dropdown Menu Value]
        //            In-house [Dropdown Menu Value]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Product Status",
            'placeholder' => "Product Status",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "TransfusionProductStatusList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));

        //    Expiration Date: [Form Field - Full Date]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Expiration Date",
            'placeholder' => "Expiration Date",
            'objectType' => $objectTypeFullDateTime,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex platelet summary",array($formNode));


        ////////////////////////////////////////////////////////
        //        Transfusion Medicine -> WinRho [Message Category]
        //Miscellaneous [Form Section]
        //    Weight: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Weight",
            'placeholder' => "Weight",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("WinRho",array($formNode));
        //    Dosing: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Dosing",
            'placeholder' => "Dosing",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("WinRho",array($formNode));
        //    IU: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "IU",
            'placeholder' => "IU",
            'objectType' => $objectTypeString,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("WinRho",array($formNode));


        //        Transfusion Medicine -> Special needs [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Special needs",array($formNode));

        //Transfusion Medicine -> Other [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Other",array($formNode),"Transfusion Medicine");

        //Microbiology [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Microbiology",array($formNode));

        //Coagulation [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Coagulation",array($formNode));

        //Hematology [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Hematology",array($formNode));

        //Chemistry [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Chemistry",array($formNode));

        //Cytogenetics [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Cytogenetics",array($formNode));

        //Molecular [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Molecular",array($formNode));

        //Other [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Other",array($formNode));


        //Transfusion Medicine -> Complex factor summary [Message Category]
        //Laboratory Values [Form Section]
        //    Relevant Laboratory Values: [Form Field - Free Text]
        $formParams = array(
            'parent' => $laboratoryValues,
            'name' => "Relevant Laboratory Values",
            'placeholder' => "Relevant Laboratory Values",
            'objectType' => $objectTypeText,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex factor summary",array($formNode));
        //Miscellaneous [Form Section]
        //    Product receiving: [Form Field - Free Text, Single Line]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Product Should Be Receiving",
            'placeholder' => "Product Should Be Receiving",
            'objectType' => $objectTypeDropdown,
            'showLabel' => true,
            'visible' => true,
            'classNamespace' => "Oleg\\UserdirectoryBundle\\Entity",
            'className' => "PlateletTransfusionProductReceivingList"
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex factor summary",array($formNode));
        //    Date: [Form Field - Full Date]
        $formParams = array(
            'parent' => $miscellaneous,
            'name' => "Date",
            'placeholder' => "Date",
            'objectType' => $objectTypeFullDateTime,
            'showLabel' => true,
            'visible' => true
        );
        $formNode = $this->createFormNode($formParams);
        $this->setFormNodeToMessageCategory("Complex factor summary",array($formNode));

    }

    /////////// NOT USED ///////////////
    public function setFormNodeToMessageCategory($messageCategoryName,$formNodes,$parentMessageCategoryName=null) {
        //attach this formnode to the MessageCategory "Transfusion Medicine"
        $em = $this->em;
        $messageCategory = null;

        $messageCategories = $em->getRepository('OlegOrderformBundle:MessageCategory')->findByName($messageCategoryName);
        if( count($messageCategories) == 0 ) {
            exit("Message categories not found by name=".$messageCategoryName);
        }
        //echo "Message categories found by name=".$messageCategoryName.": count=".count($messageCategories)."<br>";

        if( count($messageCategories) > 0 ) {
            //echo "Multiple Message Categories found: count=".count($messageCategories)."<br>";
            if( $parentMessageCategoryName ) {
                foreach( $messageCategories as $thisMessageCategory ) {
                    if( $thisMessageCategory->getParent() && $thisMessageCategory->getParent()->getName()."" == $parentMessageCategoryName ) {
                        $messageCategory = $thisMessageCategory;
                        break;
                    }
                }
                //echo "Parent found: ".$messageCategory."<br>"; //"Other"
                $this->setFormNodeToSingleMessageCategory($thisMessageCategory,$formNodes);
            }
        }

        foreach( $messageCategories as $thisMessageCategory ) {
            $this->setFormNodeToSingleMessageCategory($thisMessageCategory,$formNodes);
        }

    }
    public function setFormNodeToSingleMessageCategory($messageCategory,$formNodes) {
        $em = $this->em;
        if( !$messageCategory ) {
            exit("Message category object is not provided !!!<br>");
        }
        foreach ($formNodes as $formNode) {
            //if( !$messageCategory->getFormNode() ) {
            if ($formNode && !$messageCategory->getFormNodes()->contains($formNode)) {
                $messageCategory->addFormNode($formNode);
                $em->persist($messageCategory);
                //$em->persist($formNode);
                $em->flush();
                //echo "Add " . $formNode . " to " . $messageCategory . "<br>";
            } else {
                //echo "Node already exists " . $formNode . " in " . $messageCategory . "<br>";
            }
        }

        //clean MessageCategory: remove all formnodes from message category.
        if( count($formNodes) == 0 ) {
            //echo "Remove formnodes from " . $messageCategory . "<br>";
            foreach( $messageCategory->getFormNodes() as $thisFormNode ) {
                //echo "Removing " . $formNode . " from " . $messageCategory . "<br>";
                $messageCategory->removeFormNode($thisFormNode);
                $em->persist($messageCategory);
                $em->flush();
            }
        }
    }

    public function createFormNode( $params ) {
        exit("Depreciated. Not Used!!!");
        $em = $this->em;
        $userSecUtil = $this->container->get('user_security_utility');
        $username = $this->container->get('security.context')->getToken()->getUser();

        $objectType = $params['objectType'];
        $showLabel = $params['showLabel'];
        $name = $params['name'];
        $parent = $params['parent'];

        //placeholder
        if( array_key_exists('placeholder', $params) ) {
            $placeholder = $params['placeholder'];
        } else {
            $placeholder = null;
        }

        //visible
        if( array_key_exists('visible', $params) ) {
            $visible = $params['visible'];
        } else {
            $visible = true;
        }

        //classNamespace
        if( array_key_exists('classNamespace', $params) ) {
            $classNamespace = $params['classNamespace'];
        } else {
            $classNamespace = null;
        }

        //className
        if( array_key_exists('className', $params) ) {
            $className = $params['className'];
        } else {
            $className = null;
        }

        //classObject
        if( array_key_exists('classObject', $params) ) {
            $classObject = $params['classObject'];
        } else {
            $classObject = null;
        }

        //objectTypeList
//        if( array_key_exists('objectTypeList', $params) ) {
//            $objectTypeList = $params['objectTypeList'];
//        } else {
//            $objectTypeList = null;
//        }

        //find by name and by parent ($parent) if exists
        if( $parent ) {
            $mapper = array(
                'prefix' => "Oleg",
                'className' => "FormNode",
                'bundleName' => "UserdirectoryBundle"
            );
            //$types = array('default','user-added');
            $node = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findByChildnameAndParent($name,$parent,$mapper);
        } else {
            $node = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findOneByName($name);
            //$nodes = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findBy(array("name"=>$name,"type"=>"default"));
            //$types = array('default','user-added');
            //$node = $em->getRepository('OlegUserdirectoryBundle:FormNode')->findNodeByName($name,$types);
        }

        if( $node ) {
            if( $node->getType() == 'disabled' || $node->getType() == 'draft' ) {
                exit("The node $name already exists, but it has ".$node->getType()." type.");
            }
        }

        if( !$node ) {
            $node = new FormNode();

            $userSecUtil->setDefaultList($node,null,$username,$name);

            //set level
            $parentLevel = intval($parent->getLevel());
            $level = $parentLevel + 1;
            $node->setLevel($level);

            //set objectType
            if( $objectType ) {
                if( !$node->getObjectType() ) {
                    $node->setObjectType($objectType);
                }
            }

            //set showLabel
            $node->setShowLabel($showLabel);

            //set placeholder
            if( $placeholder) {
                $node->setPlaceholder($placeholder);
            }

            //set visible
            $node->setVisible($visible);

            //set parent
            if( $parent ) {
                $em->persist($parent);
                $parent->addChild($node);
            }

            if( $classNamespace && $className ) {
                $node->setEntityNamespace($classNamespace);
                $node->setEntityName($className);
            }

            if( $classObject ) {
                $node->setObject($classObject);
            }

            //echo "Created: ".$node->getName()."<br>";
            $em->persist($parent);
            $em->persist($node);
            $em->flush();

        } else {

            //disable all below updates when finished
            //return $node;

            $updated = false;
            //echo "Existed: ".$node->getName()."<br>";
            //echo "objectType=".$objectType->getName()."<br>";

            //set objectType
            if( $objectType ) {
                if( !$node->getObjectType() ) {
                    $node->setObjectType($objectType);
                    //echo "update objectType=".$node->getObjectType()."<br>";
                    $updated = true;
                }
            }

            if( $classNamespace && $className ) {
                $node->setEntityNamespace($classNamespace);
                $node->setEntityName($className);
                //echo "set className $classNamespace $className <br>";
                $updated = true;
            } else {
                $node->setEntityNamespace(null);
                $node->setEntityName(null);
                //echo "set NULL EntityName <br>";
                $updated = true;
            }

            if( $classObject ) {
                //echo "set  classObject=".$classObject." <br>";
                $node->setObject($classObject);
                $updated = true;
            }

            //pre-set
            if(0) {
                $node->setEntityNamespace(null);
                $node->setEntityName(null);
                $node->setEntityId(null);

                $node->setReceivedValueEntityNamespace(null);
                $node->setReceivedValueEntityName(null);
                $node->setReceivedValueEntityId(null);
                $updated = true;
            }

            if( $updated ) {
                //echo "update node=".$node." <br>";
                $em->persist($node);
                $em->flush($node);
            }

        }//if !$node

        return $node;
    }
    /////////// EOF NOT USED ///////////////

}




