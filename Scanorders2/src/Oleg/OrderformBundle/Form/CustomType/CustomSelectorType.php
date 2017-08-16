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
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/13/13
 * Time: 5:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Form\CustomType;


use Oleg\UserdirectoryBundle\Form\DataTransformer\SingleUserWrapperTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\UserWrapperTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oleg\OrderformBundle\Form\DataTransformer\ProcedureTransformer;
use Oleg\OrderformBundle\Form\DataTransformer\SourceOrganTransformer;
use Oleg\OrderformBundle\Form\DataTransformer\AccessionTypeTransformer;
use Oleg\OrderformBundle\Form\DataTransformer\MrnTypeTransformer;
use Oleg\OrderformBundle\Form\DataTransformer\AccountTransformer;
use Oleg\OrderformBundle\Form\DataTransformer\StainTransformer;

use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\StringTransformer;

class CustomSelectorType extends AbstractType {

    /**
     * @var ObjectManager
     */
    private $om;
    private $secTokenStorage;
    private $serviceContainer;

     /**
     * @param ObjectManager $om
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $secTokenStorage, $serviceContainer = null)
    {
        $this->om = $om;
        $this->secTokenStorage = $secTokenStorage;
        $this->serviceContainer = $serviceContainer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $username = $this->secTokenStorage->getToken()->getUser();
        
        $classtype = $options['classtype'];
        //echo "classtype=".$classtype."<br>";
         
        switch( $classtype ) {
            case "stain":
                $transformer = new StainTransformer($this->om, $username);
                break;
            case "staintype":
                $transformer = new StainTransformer($this->om, $username);
                break;
            case "procedureType":
                $transformer = new ProcedureTransformer($this->om, $username);
                break;         
            case "sourceOrgan":
                $transformer = new SourceOrganTransformer($this->om, $username);
                break;
            case "optionalUserEducational":
                $transformer = new UserWrapperTransformer($this->om, $this->serviceContainer, $username, 'UserWrapper');
                break;
            case "optionalUserResearch":
                $transformer = new UserWrapperTransformer($this->om, $this->serviceContainer, $username, 'UserWrapper');
                break;
            case "account":
                $transformer = new AccountTransformer($this->om, $username);
                break;
            case "accessiontype":
                $transformer = new AccessionTypeTransformer($this->om, $username);
                break;
            case "mrntype":
                $transformer = new MrnTypeTransformer($this->om, $username);
                break;
            case "userWrapper":
                $transformer = new UserWrapperTransformer($this->om, $this->serviceContainer, $username, 'UserWrapper');
                break;
            case "singleUserWrapper":
                $transformer = new SingleUserWrapperTransformer($this->om, $this->serviceContainer, $username, 'UserWrapper');
                break;
            case "parttitle":
                $transformer = new GenericTreeTransformer($this->om, $username, 'ParttitleList', 'OrderformBundle');
                break;
            case "embedderinstruction":
                $transformer = new GenericTreeTransformer($this->om, $username, 'EmbedderInstructionList', 'OrderformBundle');
                break;

            case "labtesttype":
                $transformer = new GenericTreeTransformer($this->om, $username, 'LabTestType', 'OrderformBundle');
                break;
            case "amendmentReason":
                $transformer = new GenericTreeTransformer($this->om, $username, 'AmendmentReasonList', 'OrderformBundle');
                break;

            case "projectTitle":
                $transformer = new GenericTreeTransformer($this->om, $username, 'ProjectTitleTree', 'OrderformBundle');
                break;
            case "courseTitle":
                $transformer = new GenericTreeTransformer($this->om, $username, 'CourseTitleTree', 'OrderformBundle');
                break;
            case "referringProviderSpecialty":
                $transformer = new GenericTreeTransformer($this->om, $username, 'HealthcareProviderSpecialtiesList');
                break;

//            case "returnSlide":
//                $transformer = new  GenericTreeTransformer($this->om, $username, 'Location');
//                break;
            case "scanRegion":
            case "delivery":
            case "partname":
            case "blockname":
            case "urgency":
                $transformer = new StringTransformer($this->om, $username);
                break;

            default:
                $transformer = new StringTransformer($this->om, $username);
        }
        
        
        $builder->addModelTransformer($transformer);        
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selection does not exist',
        ));
        
        $resolver->setRequired(array(
            'classtype',
        ));

//        $resolver->setAllowedTypes(array(
//            'classtype' => 'Doctrine\Common\Persistence\ObjectManager',
//        ));
        
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'custom_selector';
    }


}