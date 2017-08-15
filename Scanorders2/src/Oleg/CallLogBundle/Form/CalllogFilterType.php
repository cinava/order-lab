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

namespace Oleg\CallLogBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalllogFilterType extends AbstractType
{

    private $params;

    public function __construct( $params=null )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('startDate', 'datetime', array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'Start Date'), //'title'=>'Start Year', 'data-toggle'=>'tooltip',
        ));

        $builder->add('endDate', 'datetime', array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'End Date'), //'title'=>'End Year', 'data-toggle'=>'tooltip',
        ));

        $builder->add('entryTags', 'entity', array(
            'class' => 'OlegOrderformBundle:CalllogEntryTagsList',
            'label' => false,
            'required' => false,
            'multiple' => true,
            'attr' => array('class' => 'combobox', 'placeholder' => "Entry Tag"),
        ));

        //echo "def=".$this->params['messageCategoryDefault']."<br>";
        //print_r($this->params['messageCategories']);
        $builder->add('messageCategory', 'choice', array(
            'label' => false,
            'required' => false,
            'choices' => $this->params['messageCategories'],
            //'data' => $this->params['messageCategoryDefault'],
            'empty_data' => $this->params['messageCategoryType'],
            'attr' => array('class' => 'combobox submit-on-enter-field', 'placeholder' => "Message Type"),
        ));

//        $builder->add('mrntype', 'custom_selector', array(
//            'label'=>'MRN Type:',
//            'required' => true,
//            //'multiple' => false,
//            //'data' => 4,
//            'data' => $this->params['mrntype'],
//            'attr' => array('class' => 'ajax-combobox combobox combobox-width mrntype-combobox mrntype-exception-autogenerated'),
//            'classtype' => 'mrntype'
//        ));
        $builder->add('mrntype', 'entity', array(
            'class' => 'OlegOrderformBundle:MrnType',
            'label' => false,
            'required' => true,
            'data' => $this->params['mrntype'],
            'attr' => array('class' => 'combobox combobox-no-width', 'placeholder' => "MRN Type", 'style'=>'width:50%;'),
        ));

        //echo "formtype: search=".$this->params['search']."<br>";
        $builder->add('search', 'text', array(
            //'max_length'=>200,
            'required'=>false,
            'label' => false,
            //'data' => $this->params['search'],
            'empty_data' => $this->params['search'],
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder' => "MRN or Last Name, First Name", 'style'=>'width:50%; float:right; height:28px;'),
        ));

        $builder->add('author', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'property' => 'getUsernameOptimal',
            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Author"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->leftJoin("u.infos", "infos")
                    ->leftJoin("u.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->andWhere("(employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL)")
                    ->andWhere("(u.testingAccount = false OR u.testingAccount IS NULL)")
                    ->andWhere("(u.keytype IS NOT NULL AND u.primaryPublicUserId != 'system')")
                    ->orderBy("infos.displayName","ASC");
                //->where('u.roles LIKE :roles OR u=:user')
                //->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user']));
            },
        ));

//        $builder->add('referringProvider', 'entity', array(
//            'class' => 'OlegUserdirectoryBundle:User',
//            'label' => false,
//            'required' => false,
//            'property' => 'getUsernameOptimal',
//            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Referring Provider"),
//            'query_builder' => function (EntityRepository $er) {
//                return $er->createQueryBuilder('u')
//                    ->leftJoin("u.infos", "infos")
//                    ->leftJoin("u.employmentStatus", "employmentStatus")
//                    ->leftJoin("employmentStatus.employmentType", "employmentType")
//                    ->andWhere("(employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL)")
//                    ->andWhere("(u.testingAccount = 0 OR u.testingAccount IS NULL)")
//                    ->andWhere("(u.keytype IS NOT NULL AND u.primaryPublicUserId != 'system')")
//                    ->orderBy("infos.displayName","ASC");
//                //->where('u.roles LIKE :roles OR u=:user')
//                //->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user']));
//            },
//        ));
//        $builder->add('referringProvider', 'custom_selector', array(
//            'label' => false,
//            'attr' => array('class' => 'combobox combobox-width ajax-combobox-encounterReferringProvider', 'placeholder' => "Referring Provider"),
//            'required' => false,
//            'classtype' => 'singleUserWrapper'
//            //'classtype' => 'userWrapper'
//        ));
        $builder->add('referringProvider', 'choice', array(
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Referring Provider"),
            'choices' => $this->params['referringProviders']
        ));

        $builder->add('referringProviderSpecialty', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:HealthcareProviderSpecialtiesList',
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Specialty"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy("u.orderinlist","ASC");
            },
        ));

        $builder->add('encounterLocation', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:Location',
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Location"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->leftJoin("u.locationTypes", "locationTypes")
                    ->where("locationTypes.name='Encounter Location'")
                    ->orderBy("u.name","ASC");
            },
        ));

        $builder->add('messageStatus', 'choice', array(
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Message Status"),
            'choices' => $this->params['messageStatuses']
        ));

        //Patient List
        $builder->add('patientListTitle', 'entity', array(
            'class' => 'OlegOrderformBundle:PatientListHierarchy',
            'label' => false,
            'required' => false,
            'property' => 'name',    //'getNodeNameWithParent',
            'attr' => array('class' => 'combobox', 'placeholder' => "Patient List"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where("u.level = 3")
                    ->orderBy("u.orderinlist","ASC");
            },
        ));

        //Entry Body
        $builder->add('entryBodySearch', 'text', array(
            'required'=>false,
            'label' => false,
            'empty_data' => $this->params['entryBodySearch'],
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder' => "Entry Body"),
        ));

        //Attending
        $builder->add('attending', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'property' => 'getUsernameOptimal',
            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Attending"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->leftJoin("u.infos", "infos")
                    ->leftJoin("u.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->andWhere("(employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL)")
                    ->andWhere("(u.testingAccount = false OR u.testingAccount IS NULL)")
                    ->andWhere("(u.keytype IS NOT NULL AND u.primaryPublicUserId != 'system')")
                    ->orderBy("infos.displayName","ASC");
                //->where('u.roles LIKE :roles OR u=:user')
                //->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user']));
            },
        ));

        $mateaphoneArr = array(
            'label' => "Search similar-sounding names:",
            'required' => false,
            //'empty_data' => $this->params['metaphone'],
            //'data' => $this->params['metaphone'],
            'attr' => array('class'=>'', 'style'=>'margin:0; width: 20px;')
        );
        if( $this->params['metaphone'] ) {
            $mateaphoneArr['empty_data'] = $this->params['metaphone'];
        }
        $builder->add('metaphone', 'checkbox', $mateaphoneArr);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'filter';
    }
}
