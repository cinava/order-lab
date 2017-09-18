<?php

namespace Oleg\TranslationalResearchBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{

    protected $project;
    protected $params;

    public function formConstructor( $params )
    {
        $this->params = $params;

        $this->project = $params['project'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value']);

        //$builder->add('createDate')->add('updateDate')->add('state')->add('title')->add('irbNumber')->add('startDate')->add('expirationDate')
        //->add('funded')->add('fundedAccountNumber')->add('description')->add('budgetSummary')->add('totalCost')->add('projectType')
        //->add('biostatisticalComment')->add('administratorComment')->add('primaryReviewerComment')->add('submitter')->add('updateUser')
        //->add('principalInvestigators')->add('coInvestigators')->add('pathologists')->add('irbSubmitter')->add('contact');

        //TODO: disable all fields if routeName == "translationalresearch_project_review"

        if( $this->params['cycle'] != 'new' ) {

            $builder->add('primaryReviewerComment',null,array(
                'label' => "Primary Reviewer Comment:",
                'attr' => array('class'=>'textarea form-control')
            ));

            $builder->add('state',null, array(
                'label' => 'State:',
                //'disabled' => true,
                'required' => false,
                'attr' => array('class' => 'form-control'),
            ));

            $builder->add('approvalDate', DateType::class, array(
                'widget' => 'single_text',
                'label' => "Approval Date:",
                //'disabled' => true,
                'format' => 'MM/dd/yyyy',
                'attr' => array('class' => 'datepicker form-control'),
                'required' => false,
            ));
        }

        if( $this->project->getCreateDate() ) {
            $builder->add('createDate', DateType::class, array(
                'widget' => 'single_text',
                'label' => "Create Date:",
                //'disabled' => true,
                'format' => 'MM/dd/yyyy',
                'attr' => array('class' => 'datepicker form-control', 'readonly'=>true),
                'required' => false,
            ));

            $builder->add('submitter', null, array(
                'label' => "Created By:",
                //'disabled' => true,
                'attr' => array('class'=>'combobox combobox-width', 'readonly'=>true)
            ));
        }

//        if( $this->project->getUpdateDate() ) {
//            $builder->add('updateDate', 'date', array(
//                'widget' => 'single_text',
//                'label' => "Update Date:",
//                'disabled' => true,
//                'format' => 'MM/dd/yyyy',
//                'attr' => array('class' => 'datepicker form-control'),
//                'required' => false,
//            ));
//        }
//        if( $this->project->getUpdateUser() ) {
//            $builder->add('updateUser', null, array(
//                'label' => "Updated By:",
//                'disabled' => true,
//            ));
//        }

        $builder->add('title',null,array(
            'required' => false,
            'label'=>"Project Title:",
            'attr' => array('class'=>'textarea form-control')
        ));

        $builder->add('irbNumber',null, array(
            'label' => 'IRB Number:',
            'required' => false,
            'attr' => array('class' => 'form-control'),
        ));

//        $builder->add('startDate','date',array(
//            'widget' => 'single_text',
//            'label' => "Project Start Date:",
//            'format' => 'MM/dd/yyyy',
//            'attr' => array('class' => 'datepicker form-control'),
//            'required' => false,
//        ));
//
//        $builder->add('expirationDate','date',array(
//            'widget' => 'single_text',
//            'label' => "Project Expiration Date:",
//            'format' => 'MM/dd/yyyy',
//            'attr' => array('class' => 'datepicker form-control'),
//            'required' => false,
//        ));

        $builder->add('funded',CheckboxType::class,array(
            'required' => false,
            'label'=>"Is this Research Project Funded:",
            'attr' => array('class'=>'form-control')
        ));

        $builder->add('fundedAccountNumber',null, array(
            'label' => 'If funded, please provide account number:',
            'required' => false,
            'attr' => array('class' => 'form-control'),
        ));

//        $descriptionLabel =
//            "Please provide a brief description of the project to include background information,
//            purpose and objective, and a methodology section stating a justification for
//            the size and scope of the project. The breadth of information
//            should be adequate for a scientific committee to understand and assess the value of the research.";
        $descriptionLabel = "Brief Description";
        $builder->add('description',null,array(
            'label' => $descriptionLabel,
            'attr' => array('class'=>'textarea form-control') //,'style'=>'height:300px'
        ));

        $builder->add('budgetSummary',null,array(
            'label' => "Provide a Detailed Budget Outline/Summary:",
            'attr' => array('class'=>'textarea form-control')
        ));

        $builder->add('totalCost',null, array(
            'label' => 'Estimated Total Costs ($):',
            'required' => false,
            //'attr' => array('class' => 'form-control', 'data-inputmask' => "'alias': 'currency'", 'style'=>'text-align: left !important;' )
            'attr' => array('class' => 'form-control currency-mask mask-text-align-left'),
        ));

        $builder->add('projectType',null, array(
            'label' => 'Project Type:',
            'required' => false,
            'attr' => array('class' => 'form-control'),
        ));

        $builder->add('biostatisticalComment',null,array(
            'label' => "Biostatistical Comment:",
            'attr' => array('class'=>'textarea form-control')
        ));

        $builder->add('administratorComment',null,array(
            'label' => "Administrator Comment:",
            'attr' => array('class'=>'textarea form-control')
        ));

        $builder->add('readyForReview', CheckboxType::class, array(
            'required' => false,
            'label' => "Please check the box if this project is ready for committee to review:",
            'attr' => array('class' => 'form-control')
        ));

        $builder->add( 'principalInvestigators', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label'=> "Principal Investigator(s):",
            'required'=> false,
            'multiple' => true,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName","ASC");
            },
        ));

        $builder->add( 'coInvestigators', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label'=> "Co-Investigator(s):",
            'required'=> false,
            'multiple' => true,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName","ASC");
            },
        ));

        $builder->add( 'irbSubmitter', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label'=> "Name of PI Who Submitted the IRB:",
            'required'=> false,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName","ASC");
            },
        ));

        $builder->add( 'pathologists', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label'=> "WCMC Pathologist Involved:",
            'required'=> false,
            'multiple' => true,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName","ASC");
            },
        ));

        $builder->add( 'contact', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label'=> "Contact:",
            'required'=> false,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->leftJoin("list.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                    ->leftJoin("list.infos", "infos")
                    ->orderBy("infos.displayName","ASC");
            },
        ));

        //Reviews
        //$showIrbReviews = true;
        //$showAdminReviews = true;
        //$showCommitteeReviews = true;
        //$showFinalReviews = true;

        //echo "showIrbReviews=".$this->params['showIrbReviews']."<br>";
        if( $this->params['showIrbReviews'] ) {
            //echo "show irb<br>";
            $this->params['stateStr'] = "irb_review";
            $builder->add('irbReviews', CollectionType::class, array(
                'entry_type' => ReviewBaseType::class,
                'entry_options' => array(
                    'data_class' => 'Oleg\TranslationalResearchBundle\Entity\IrbReview',
                    'form_custom_value' => $this->params
                ),
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__irbreviews__',
            ));
        }

        if( $this->params['showAdminReviews'] ) {
            $this->params['stateStr'] = "admin_review";
            $builder->add('adminReviews', CollectionType::class, array(
                'entry_type' => ReviewBaseType::class,
                'entry_options' => array(
                    'data_class' => 'Oleg\TranslationalResearchBundle\Entity\AdminReview',
                    'form_custom_value' => $this->params
                ),
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__adminreviews__',
            ));
        }

        if( $this->params['showCommitteeReviews'] ) {
            $this->params['stateStr'] = "committee_review";
            $builder->add('committeeReviews', CollectionType::class, array(
                'entry_type' => ReviewBaseType::class,
                'entry_options' => array(
                    'data_class' => 'Oleg\TranslationalResearchBundle\Entity\CommitteeReview',
                    'form_custom_value' => $this->params
                ),
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__committeereviews__',
            ));
        }

        if( $this->params['showFinalReviews'] ) {
            $this->params['stateStr'] = "final_review";
            $builder->add('finalReviews', CollectionType::class, array(
                'entry_type' => ReviewBaseType::class,
                'entry_options' => array(
                    'data_class' => 'Oleg\TranslationalResearchBundle\Entity\FinalReview',
                    'form_custom_value' => $this->params
                ),
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__finalreviews__',
            ));
        }

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\TranslationalResearchBundle\Entity\Project',
            'form_custom_value' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oleg_translationalresearchbundle_project';
    }


}
