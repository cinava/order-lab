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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalllogFilterType extends AbstractType
{

    private $params;

    public function formConstructor( $params=null )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value']);

        $builder->add('startDate', DateTimeType::class, array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'Start Date'), //'title'=>'Start Year', 'data-toggle'=>'tooltip',
        ));

        $builder->add('endDate', DateTimeType::class, array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'End Date'), //'title'=>'End Year', 'data-toggle'=>'tooltip',
        ));

        $builder->add('entryTags', EntityType::class, array(
            'class' => 'OlegOrderformBundle:CalllogEntryTagsList',
            'label' => false,
            'required' => false,
            'multiple' => true,
            'attr' => array('class' => 'combobox', 'placeholder' => "Entry Tag(s)"),
        ));

        //echo "def=".$this->params['messageCategoryDefault']."<br>";
        //print_r($this->params['messageCategories']);
        $builder->add('messageCategory', ChoiceType::class, array(
            'label' => false,
            'required' => false,
            'choices' => $this->params['messageCategories'],
            'choices_as_values' => true,
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
        //echo "form mrntype=".$this->params['mrntype']."<br>";
//        $builder->add('mrntype', EntityType::class, array(
//            'class' => 'OlegOrderformBundle:MrnType',
//            'label' => false,
//            //'required' => true,
//            'required' => false,
//            //'mapped' => false,
//            'data' => $this->params['mrntype'],
//            //'data' => 'ssss',
//            //'empty_data' => $this->params['mrntype'],
//            'attr' => array('class' => 'combobox combobox-no-width', 'placeholder' => "MRN Type", 'style'=>'width:50%;'),
//        ));
//        echo "form mrntype=".$this->params['mrntypeDefault']."<br>";
        $builder->add('mrntype', ChoiceType::class, array(
            'label' => false,
            //'required' => true,
            'required' => false,
            'choices' => $this->params['mrntypeChoices'],
            'choices_as_values' => true,
            'data' => $this->params['mrntypeDefault'],
            //'data' => 'Epic Ambulatory Enterprise ID Number',
            //'empty_data' => $this->params['mrntypeDefault'],
            //'empty_data' => 'Epic Ambulatory Enterprise ID Number',
            'attr' => array('class' => 'combobox combobox-no-width', 'placeholder' => "MRN Type", 'style'=>'width:50%;'),
        ));

        //echo "formtype: search=".$this->params['search']."<br>";
        $builder->add('search', TextType::class, array(
            //'max_length'=>200,
            'required'=>false,
            'label' => false,
            //'data' => $this->params['search'],
            'empty_data' => $this->params['search'],
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder' => "MRN or Last Name, First Name", 'style'=>'width:50%; float:right; height:28px;'),
        ));

        $builder->add('author', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'choice_label' => 'getUsernameOptimal',
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

//        $builder->add('referringProvider', EntityType::class, array(
//            'class' => 'OlegUserdirectoryBundle:User',
//            'label' => false,
//            'required' => false,
//            'choice_label' => 'getUsernameOptimal',
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
        $builder->add('referringProvider', ChoiceType::class, array(
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Referring Provider"),
            'choices' => $this->params['referringProviders'],
            'choices_as_values' => true,
        ));

        $builder->add('referringProviderSpecialty', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:HealthcareProviderSpecialtiesList',
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Specialty"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy("u.orderinlist","ASC");
            },
        ));

        $builder->add('encounterLocation', EntityType::class, array(
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

        $builder->add('messageStatus', ChoiceType::class, array(
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Message Status"),
            'choices' => $this->params['messageStatuses'],
            'choices_as_values' => true,
        ));

        //Patient List
        $builder->add('patientListTitle', EntityType::class, array(
            'class' => 'OlegOrderformBundle:PatientListHierarchy',
            'label' => false,
            'required' => false,
            'choice_label' => 'name',    //'getNodeNameWithParent',
            'attr' => array('class' => 'combobox', 'placeholder' => "Patient List"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where("u.level = 3")
                    ->orderBy("u.orderinlist","ASC");
            },
        ));

        //Entry Body
        $builder->add('entryBodySearch', TextType::class, array(
            'required'=>false,
            'label' => false,
            'empty_data' => $this->params['entryBodySearch'],
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder' => "Entry Text"),
        ));

        //Attending
        $builder->add('attending', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'choice_label' => 'getUsernameOptimal',
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
        $builder->add('metaphone', CheckboxType::class, $mateaphoneArr);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'form_custom_value' => null,
            'csrf_protection' => false,
        ));
    }

    public function getBlockPrefix()
    {
        return 'filter';
    }
}
