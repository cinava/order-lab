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

namespace App\ResAppBundle\Form;


use App\UserdirectoryBundle\Form\BoardCertificationType;
use App\UserdirectoryBundle\Form\CitizenshipType;
use App\UserdirectoryBundle\Form\DataTransformer\StringToBooleanTransformer;
use App\UserdirectoryBundle\Form\DocumentType;
use App\UserdirectoryBundle\Form\ExaminationType;
use App\UserdirectoryBundle\Form\LocationType;
use App\UserdirectoryBundle\Form\StateLicenseType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class ResidencyApplicationType extends AbstractType
{

    protected $params;

    public function formConstructor( $params=null )
    {
        $this->params = $params;
//        foreach($params as $key=>$value) {
//            if( $key != "user" && $key != "em" && $key != "container" ) {
//                echo $key.": value=".$value."<br>";
//                print_r($value);
//            }
//        }
        //exit();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->formConstructor($options['form_custom_value']);

//        $builder->add('residencySubspecialty',null, array(
//            'label' => '* Residency Type:',
//            'required' => false,
//            'attr' => array('class' => 'combobox combobox-width resapp-residencySubspecialty'),
//        ));
//        $builder->add('residencySubspecialty', 'entity', array(
//            'class' => 'AppUserdirectoryBundle:ResidencySpecialty',
//            'label'=> "* Residency Application Type:",
//            'required'=> false,
//            //'multiple' => true,
//            'attr' => array('class'=>'combobox combobox-width resapp-residencySubspecialty'),
//            'query_builder' => function(EntityRepository $er) {
//                return $er->createQueryBuilder('list')
//                    ->leftJoin("list.institution","institution")
//                    ->where("list.type = :typedef OR list.type = :typeadd")
//                    ->andWhere("institution.id IS NOT NULL")
//                    ->orderBy("list.orderinlist","ASC")
//                    ->setParameters( array(
//                        'typedef' => 'default',
//                        'typeadd' => 'user-added',
//                    ));
//            },
//        ));


        //get subresidency types as for ROLE_RESAPP_ADMIN
        //$resappUtil = $this->params['container']->get('resapp_util');
        //$resTypes = $resappUtil->getResidencyTypesByInstitution(true);

        if( array_key_exists('resappTypes', $this->params) ) {
            $resappChoices = $this->params['resappTypes'];
        } else {
            $resappChoices = array();
        }

        $builder->add('residencySubspecialty', EntityType::class, array(
            'class' => 'AppUserdirectoryBundle:ResidencySubspecialty',
            'label' => "Residency Application Type:",
            //'required' => true,
            'required' => false,
            'choices' => $resappChoices,   //$this->params['resappTypes'], //$resTypes,
            'invalid_message' => 'residencySubspecialty invalid value',
            //'choices_as_values' => true,
            'attr' => array('class' => 'combobox combobox-width resapp-residencySubspecialty'),
        ));

        if( 0 && $this->params['cycle'] == "edit" ) {
            //$this->secAuthChecker->isGranted('ROLE_RESAPP_ADMIN') ||
            //if( $this->secAuthChecker->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            if( $this->params['container']->get('security.authorization_checker')->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
                $builder->add('googleFormId', null, array(
                    'required' => false,
                    'label' => "Google Form Id (Do not modify this value! New application will be generated if googleFormId will be different from the one in the spreadsheet.):",
                    'attr' => array('class' => 'form-control')
                ));
            }
        }

        if ($this->params['cycle'] == "new") {
            $builder->add('timestamp', DateType::class, array(
                'widget' => 'single_text',
                'label' => "Application Receipt Date:",
                //'format' => 'MM/dd/yyyy, H:mm:ss',
                'format' => 'MM/dd/yyyy',
                'attr' => array('class' => 'datepicker form-control'),
                'required' => false,
            ));
        }

        $builder->add('startDate', DateType::class, array(
            'widget' => 'single_text',
            'label' => "Start Date:",
            'format' => 'MM/dd/yyyy',  //'MM/dd/yyyy, H:mm:ss',
            'attr' => array('class' => 'datepicker form-control resapp-startDate'),
            'required' => false,
        ));

        $builder->add('endDate', DateType::class, array(
            'widget' => 'single_text',
            'label' => "End Date:",
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control resapp-endDate'),
            'required' => false,
        ));

        //ResAppUserType($this->params)
        $builder->add('user', ResAppUserType::class, array(
            'form_custom_value' => $this->params,
            'data_class' => 'App\UserdirectoryBundle\Entity\User',
            'label' => false,
            'required' => false,
        ));

        //return false;

        $builder->add('coverLetters', CollectionType::class, array(
            //'type' => new DocumentType($this->params),
            'entry_type' => DocumentType::class,
            'label' => 'Cover Letter(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        $builder->add('cvs', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Curriculum Vitae (CV):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));


        //        $builder->add('reprimand','choice', array(
        //            'label' => 'Have you ever been reprimanded, or had your license suspended or revoked in any of these states?',
        //            'required' => false,
        //            'choices' => array('Yes'=>'Yes','No'=>'No'),
        //            'attr' => array('class' => 'combobox'),
        //        ));
        $builder->add('reprimand', CheckboxType::class, array(
            'label' => 'Have you ever been reprimanded, or had your license suspended or revoked in any of these states?',
            'required' => false,
            'attr' => array('class' => 'form-control resapp-reprimand-field', 'onclick' => 'showHideWell(this)'),
        ));
        $builder->get('reprimand')->addModelTransformer(new StringToBooleanTransformer());
        $builder->add('reprimandDocuments', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Upload Reprimand Explanation(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        //        $builder->add('lawsuit','choice', array(
        //            'label' => 'Have you ever been reprimanded, or had your license suspended or revoked in any of these states?',
        //            'required' => false,
        //            'choices' => array('Yes'=>'Yes','No'=>'No'),
        //            'attr' => array('class' => 'combobox'),
        //        ));
        $builder->add('lawsuit', CheckboxType::class, array(
            'label' => 'Have you ever been named in (and/or had a judgment against you) in a medical malpractice legal suit?',
            'required' => false,
            'attr' => array('class' => 'form-control resapp-lawsuit-field', 'onclick' => 'showHideWell(this)'),
        ));
        $builder->get('lawsuit')->addModelTransformer(new StringToBooleanTransformer());
        $builder->add('lawsuitDocuments', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Upload Legal Explanation(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));


        $builder->add('references', CollectionType::class, array(
            'entry_type' => ReferenceType::class,
            'entry_options' => array(
                'form_custom_value' => $this->params
            ),
            'label' => 'Reference(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__references__',
        ));


        $builder->add('honors', null, array(
            'required' => false,
            'label' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('publications', null, array(
            'required' => false,
            'label' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('memberships', null, array(
            'required' => false,
            'label' => false,
            'attr' => array('class' => 'textarea form-control')
        ));


        $builder->add('signatureName', null, array(
            'label' => 'Signature:',
            'required' => false,
            'attr' => array('class' => 'form-control'),
        ));

        $builder->add('signatureDate', null, array(
            'label' => 'Date:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
        ));


        $builder->add('reports', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Report(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        $builder->add('formReports', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Form Report(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));


        $builder->add('oldReports', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Old Report(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));


        //other documents
        $builder->add('documents', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Other Document(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        $builder->add('itinerarys', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Itinerary / Interview Schedule(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));


        $builder->add('interviewDate', null, array(
            'label' => 'Interview Date:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
        ));

        $builder->add('interviews', CollectionType::class, array(
            'entry_type' => InterviewType::class,
            'entry_options' => array(
                'form_custom_value' => $this->params
            ),
            'label' => 'Interview(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__interviews__',
        ));

        $builder->add('observers', EntityType::class, array(
            'class' => 'AppUserdirectoryBundle:User',
            'label' => "Observer(s):",
            'required' => false,
            'multiple' => true,
            'attr' => array('class' => 'combobox combobox-width'),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Residency Applicant' OR employmentType.id IS NULL")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName", "ASC");
            },
        ));


        /////////////////// user objects ////////////////////////////

        $builder->add('avatars', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Applicant Photo(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        $builder->add('trainings', CollectionType::class, array(
            'entry_type' => ResAppTrainingType::class,
            'entry_options' => array(
                'form_custom_value' => $this->params
            ),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__trainings__',
        ));

        $this->userLocations($builder);

        $builder->add('citizenships', CollectionType::class, array(
            'entry_type' => CitizenshipType::class,
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__citizenships__',
        ));

        $builder->add('examinations', CollectionType::class, array(
            'entry_type' => ExaminationType::class,
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__examinations__',
        ));

        $builder->add('stateLicenses', CollectionType::class, array(
            'entry_type' => StateLicenseType::class,
            'entry_options' => array(
                'form_custom_value' => $this->params
            ),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__statelicenses__',
        ));

        $builder->add('boardCertifications', CollectionType::class, array(
            'entry_type' => BoardCertificationType::class,
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__boardcertifications__',
        ));

        //////////////////////////////////////////////////////////////


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ResAppBundle\Entity\ResidencyApplication',
            'form_custom_value' => null,
            'csrf_protection' => false
        ));
    }

    public function getBlockPrefix()
    {
        return 'oleg_resappbundle_residencyapplication';
    }



    public function userLocations($builder) {


        if( $this->params['container']->get('security.authorization_checker')->isGranted('ROLE_RESAPP_COORDINATOR') ) {
            $roleAdmin = true;
            $readonly = true;
        } else {
            $roleAdmin = false;
            $readonly = false;
        }
        //echo "readonly=".$readonly."<br>";
        $readonly = false;

        $currentUser = false;
        $user = $this->params['container']->get('security.token_storage')->getToken()->getUser();
        if( $user->getId() === $this->params['user']->getId() ) {
            $currentUser = true;
        }
        //echo "currentUser=".$currentUser."<br>";


        $params = array('disabled'=>$readonly,'admin'=>$roleAdmin,'currentUser'=>$currentUser,'cycle'=>$this->params['cycle'],'em'=>$this->params['em'],'subjectUser'=>$this->params['user']);

        $builder->add('locations', CollectionType::class, array(
            'entry_type' => ResAppLocationType::class,
            'entry_options' => array(
                'form_custom_value' => $params
            ),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__locations__',
        ));

        return $builder;
    }

}
