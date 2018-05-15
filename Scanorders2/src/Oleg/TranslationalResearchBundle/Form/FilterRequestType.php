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

namespace Oleg\TranslationalResearchBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterRequestType extends AbstractType
{

    private $params;

    public function formConstructor( $params=null )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value']);

        //if( $this->params['routeName'] != "translationalresearch_my_requests" ) {
            $builder->add('submitter', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:User',
                'label' => "Reviewer Delegate:",
                'required' => false,
                'multiple' => false,
                'attr' => array('class' => 'combobox combobox-width'),
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('list')
                        ->leftJoin("list.employmentStatus", "employmentStatus")
                        ->leftJoin("employmentStatus.employmentType", "employmentType")
                        ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                        //->andWhere("list.roles LIKE '%ROLE_TRANSRES_%'")
                        ->leftJoin("list.infos", "infos")
                        ->orderBy("infos.displayName", "ASC");
                },
            ));
        //}
        
        if( $this->params['routeName'] == "translationalresearch_request_index" ) {
            $builder->add('project', EntityType::class, array(
                'class' => 'OlegTranslationalResearchBundle:Project',
                'choice_label' => "getProjectInfoNameChoice",
                'required' => false,
                'label' => false,
                'data' => $this->params['project'],
                'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Project"),
            ));
        } else {
//            $builder->add('project', EntityType::class, array(
//                'class' => 'OlegTranslationalResearchBundle:Project',
//                'choice_label' => "getProjectInfoName",
//                'required' => false,
//                'label' => false,
//                'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Project"),
//            ));
            $builder->add('project', EntityType::class, array(
                'class' => 'OlegTranslationalResearchBundle:Project',
                'choice_label' => 'getProjectInfoNameChoice',
                'choices' => $this->params['availableProjects'],
                //'disabled' => ($this->params['admin'] ? false : true),
                //'disabled' => true,
                'required' => false,
                'multiple' => false,
                'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Project")
            ));
        }
        
        $builder->add('comment', TextType::class, array(
            'required'=>false,
            'label' => false,
            'attr' => array('class'=>'form-control form-control-modif limit-font-size submit-on-enter-field'),
        ));

        $builder->add( 'category', EntityType::class, array(
            'class' => 'OlegTranslationalResearchBundle:RequestCategoryTypeList',
            'label'=> false,
            'choice_label' => "getOptimalAbbreviationName",
            'required'=> false,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->where("list.type = :typedef OR list.type = :typeadd")
                    ->orderBy("list.orderinlist","ASC")
                    ->setParameters( array(
                        'typedef' => 'default',
                        'typeadd' => 'user-added',
                    ));
            },
        ));

        $builder->add('progressState',ChoiceType::class, array(
            'label' => false,
            'required' => false,
            'multiple' => true,
            'choices' => $this->params['progressStateArr'],
            'attr' => array('class' => 'combobox'),
        ));

        $builder->add('billingState',ChoiceType::class, array(
            'label' => false,
            'required' => false,
            'multiple' => true,
            'choices' => $this->params['billingStateArr'],
            'attr' => array('class' => 'combobox'),
        ));

        $builder->add('startDate', DateTimeType::class, array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'From Submission Date'), //'title'=>'Start Year', 'data-toggle'=>'tooltip',
        ));

        $builder->add('endDate', DateTimeType::class, array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'To Submission Date'), //'title'=>'End Year', 'data-toggle'=>'tooltip',
        ));

//        $builder->add('accountNumber', TextType::class, array(
//            'required'=>false,
//            'label' => false,
//            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder'=>'Search by IRB number'),
//        ));

        $builder->add('billingContact', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'multiple' => false,
            'attr' => array('class' => 'combobox combobox-width'),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                    //->andWhere("list.roles LIKE '%ROLE_TRANSRES_%'")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName", "ASC");
            },
        ));

        $builder->add('principalInvestigators', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'multiple' => true,
            'attr' => array('class' => 'combobox combobox-width'),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                    //->andWhere("list.roles LIKE '%ROLE_TRANSRES_%'")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName", "ASC");
            },
        ));

        $projectSpecialtyAllowedArr = array();
        foreach($this->params["projectSpecialtyAllowedArr"] as $spec) {
            $projectSpecialtyAllowedArr[] = $spec;
        }

        if( count($projectSpecialtyAllowedArr) == 1 ) {
            $disabled = true;
        } else {
            $disabled = false;
        }

        $builder->add('projectSpecialty', EntityType::class, array(
            'class' => 'OlegTranslationalResearchBundle:SpecialtyList',
            'label' => false,   //'Project Specialty',
            'required'=> false,
            'multiple' => true,
            'disabled' => $disabled,
            'choices' => $this->params["projectSpecialtyAllowedArr"],
            'data' => $projectSpecialtyAllowedArr,
            'attr' => array('class'=>'combobox combobox-width'),
//            'query_builder' => function(EntityRepository $er) {
//                return $er->createQueryBuilder('list')
//                    ->where("list.type = :typedef OR list.type = :typeadd")
//                    ->orderBy("list.orderinlist","ASC")
//                    ->setParameters( array(
//                        'typedef' => 'default',
//                        'typeadd' => 'user-added',
//                    ));
//            },
        ));

        $builder->add('fundingNumber', TextType::class, array(
            'required'=>false,
            'label' => false,
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder'=>'Search by Fund Number'),
        ));

        $builder->add('fundingType',ChoiceType::class, array(
            'label' => false,
            'required' => false,
            'multiple' => false,
            'choices' => array(
                'Funded (With Fund Number)' => 'Funded',
                'Non-Funded (No Fund Number)' => 'Non-Funded'
            ),
            'attr' => array('class' => 'combobox', 'placeholder'=>'Funded vs Non-Funded'),
        ));

        $builder->add('externalId', TextType::class, array(
            'required'=>false,
            'label' => false,
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder'=>'External ID'),
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'form_custom_value' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'filter';
    }
}
