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

namespace Oleg\UserdirectoryBundle\Form\CustomType;

use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericManytomanyTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericSelectTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\IntegerCheckboxTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\MonthYearDateTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\ResearchLabTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

use Oleg\UserdirectoryBundle\Form\DataTransformer\StringTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;

class CustomSelectorType extends AbstractType {

    /**
     * @var ObjectManager
     * @var SecurityContext
     */
    private $om;
    private $sc;
    private $serviceContainer;

     /**
     * @param ObjectManager $om
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, SecurityContext $sc, $serviceContainer = null)
    {
        $this->om = $om;
        $this->sc = $sc;
        $this->serviceContainer = $serviceContainer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $username = $this->sc->getToken()->getUser();
        
        $classtype = $options['classtype'];

        switch( $classtype ) {
            case "institution":
                $params = array('type'=>'Medical');
                $transformer = new GenericTreeTransformer($this->om, $username, 'Institution', null, $params);
                break;
            case "institution-many":
                $transformer = new GenericManytomanyTransformer($this->om, $username, 'Institution');
                break;
            case "commenttype":
                $transformer = new GenericTreeTransformer($this->om, $username, 'CommentTypeList');
                break;
            case "messageCategory":
                $transformer = new GenericTreeTransformer($this->om, $username, 'MessageCategory', 'OrderformBundle');
                break;
            case "patientList":
                $transformer = new GenericTreeTransformer($this->om, $username, 'PatientListHierarchy', 'OrderformBundle');
                break;
//            case "institution_id":
//                $params = array('field'=>'id');
//                $transformer = new GenericTreeTransformer($this->om, $username, 'Institution', null, $params);
//                break;
//            case "department":
//                $transformer = new GenericTreeTransformer($this->om, $username, 'Department');
//                break;
//            case "division":
//                $transformer = new GenericTreeTransformer($this->om, $username, 'Division');
//                break;
//            case "service":
//                $transformer = new GenericTreeTransformer($this->om, $username, 'Service');
//                break;
            case "identifierkeytype":
                $transformer = new GenericTreeTransformer($this->om, $username, 'IdentifierTypeList');
                break;
            case "fellowshiptype":
                $transformer = new GenericTreeTransformer($this->om, $username, 'FellowshipTypeList');
                break;
            case "researchlab":
                $transformer = new ResearchLabTransformer($this->om, $username, 'ResearchLab');
                break;
            case "location":
                $transformer = new GenericTreeTransformer($this->om, $username, 'Location');
                break;
            case "building":
                $transformer = new GenericTreeTransformer($this->om, $username, 'BuildingList');
                break;
            case "city":
                $transformer = new GenericTreeTransformer($this->om, $username, 'CityList');
                break;
            case "organization":
                $transformer = new GenericTreeTransformer($this->om, $username, 'OrganizationList');
                break;
            case "room":
                $transformer = new GenericTreeTransformer($this->om, $username, 'RoomList');
                break;
            case "suite":
                $transformer = new GenericTreeTransformer($this->om, $username, 'SuiteList');
                break;
            case "floor":
                $transformer = new GenericTreeTransformer($this->om, $username, 'FloorList');
                break;
            case "mailbox":
                $transformer = new GenericTreeTransformer($this->om, $username, 'MailboxList');
                break;
            case "effort":
                $transformer = new GenericTreeTransformer($this->om, $username, 'EffortList');
                break;
            case "administrativetitletype":
                $transformer = new GenericTreeTransformer($this->om, $username, 'AdminTitleList');
                break;
            case "appointmenttitletype":
                $transformer = new GenericTreeTransformer($this->om, $username, 'AppTitleList');
                break;
            case "medicaltitletype":
                $transformer = new GenericTreeTransformer($this->om, $username, 'MedicalTitleList');
                break;

            //training (7 from 9)
            case "traininginstitution":
                $params = array('type'=>'Educational');
                $transformer = new GenericTreeTransformer($this->om, $username, 'Institution', null, $params);
                break;
            case "trainingmajors":
                $transformer = new GenericManytomanyTransformer($this->om, $username, 'MajorTrainingList');
                break;
            case "trainingminors":
                $transformer = new GenericManytomanyTransformer($this->om, $username, 'MinorTrainingList');
                break;
            case "traininghonors":
                $transformer = new GenericManytomanyTransformer($this->om, $username, 'HonorTrainingList');
                break;
            case "trainingfellowshiptitle":
                $transformer = new GenericTreeTransformer($this->om, $username, 'FellowshipTitleList');
                break;
            case "residencyspecialty":
                $transformer = new GenericTreeTransformer($this->om, $username, 'ResidencySpecialty');
                break;
            case "fellowshipsubspecialty":
                $transformer = new GenericTreeTransformer($this->om, $username, 'FellowshipSubspecialty');
                break;
            case "locationusers":
                $transformer = new GenericTreeTransformer($this->om, $username, 'User');
                break;
            case "jobTitle":
                $transformer = new GenericTreeTransformer($this->om, $username, 'JobTitleList');
                break;
            case "referringProviderSpecialty":
                $transformer = new GenericTreeTransformer($this->om, $username, 'HealthcareProviderSpecialtiesList');
                break;
            case "locationName":
                $transformer = new GenericSelectTransformer($this->om, $username, 'Location');
                break;

            //grants
            case "sourceorganization":
                $transformer = new GenericTreeTransformer($this->om, $username, 'SourceOrganization');
                break;


//            //labtesttype
//            case "labtesttype":
//                $transformer = new GenericTreeTransformer($this->om, $username, 'LabTestType', 'OrderformBundle');
//                break;

            //month year date only
            case "month_year_date_only":
                $transformer = new MonthYearDateTransformer($this->om, $username);
                break;

            case "grant":
                $transformer = new ResearchLabTransformer($this->om, $username, 'Grant');
                break;

            default:
                $transformer = new StringTransformer($this->om, $username);
        }
        
        
        $builder->addModelTransformer($transformer);        
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'employees_custom_selector';
    }


}