<?php

namespace Oleg\TranslationalResearchBundle\Form;

use Doctrine\ORM\EntityRepository;
use Oleg\UserdirectoryBundle\Form\DocumentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{

    protected $invoice;
    protected $params;

    public function formConstructor( $params )
    {
        $this->params = $params;
        $this->invoice = $params['invoice'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value']);

        //$builder->add('createDate')->add('updateDate')->add('oid')->add('invoiceNumber')->add('dueDate')->add('status')->add('to')->add('discountNumeric')->add('discountPercent')->add('submitter')->add('updateUser')->add('transresRequests')->add('salesperson');

//        $builder->add('createDate', DateType::class, array(
//            'widget' => 'single_text',
//            'label' => "Create Date:",
//            'disabled' => true,
//            'format' => 'MM/dd/yyyy',
//            'attr' => array('class' => 'datepicker form-control'),
//            'required' => false,
//        ));
        
        $builder->add('status', ChoiceType::class, array( //flipped
            'label' => 'Status',
//            'choices' => array(
//                "Pending" => "Pending",
//                "Unpaid/Issued" => "Unpaid/Issued",
//                "Paid in Full" => "Paid in Full",
//                "Paid Partially" => "Paid Partially",
//                "Canceled" => "Canceled"
//            ),
            'choices' => $this->params['statuses'],
            'multiple' => false,
            'required' => true,
            'attr' => array('class' => 'combobox combobox-width')
        ));

//        $builder->add('principalInvestigators', EntityType::class, array(
//            'class' => 'OlegUserdirectoryBundle:User',
//            'label'=> "Principal Investigator(s):",
//            'required'=> false,
//            'multiple' => false,
//            'attr' => array('class'=>'combobox combobox-width transres-invoice-principalInvestigator'),
//            'choices' => $this->params['principalInvestigators']
////            'query_builder' => function(EntityRepository $er) {
////                return $er->createQueryBuilder('list')
////                    ->leftJoin("list.employmentStatus", "employmentStatus")
////                    ->leftJoin("employmentStatus.employmentType", "employmentType")
////                    ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
////                    ->andWhere("list.roles LIKE '%ROLE_TRANSRES_%'")
////                    ->leftJoin("list.infos", "infos")
////                    ->orderBy("infos.displayName","ASC");
////            },
//        ));
        $builder->add('principalInvestigator', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label'=> "Principal Investigator:",
            'required'=> true,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width transres-invoice-principalInvestigator'),
            'choices' => $this->params['principalInvestigators'],
            //'by_reference' => true
            //'em' => $this->params['em'],
            //'data' => $this->params['principalInvestigators']
        ));

        $builder->add('salesperson', EntityType::class, array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => "Salesperson:",
            //'disabled' => true,
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

        if(0) {
            $builder->add('submitter', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:User',
                'label' => "Submitter:",
                'disabled' => true,
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
        }

        //if( $this->params['cycle'] != 'new' ) {
            $builder->add('oid', null, array(
                'label' => "Invoice Number:",
                'disabled' => true,
                'required' => false,
                'attr' => array('class' => 'form-control')
            ));
        //}

        $builder->add('fundedAccountNumber', null, array(
            'label' => "WCM account number (if funded):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('dueDate', DateType::class, array(
            'widget' => 'single_text',
            'label' => "Due Date:",
            //'disabled' => true,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
            'required' => false,
        ));

        $builder->add('invoiceFrom', null, array(
            'label' => "From:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('invoiceTo', null, array(
            'label' => "To:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control transres-invoice-invoiceTo')
        ));

        $builder->add('discountNumeric', null, array(
            'label' => "Discount ($):",
            //'disabled' => true,
            'required' => false,
            'attr' => array('class' => 'form-control invoice-discountNumeric')
        ));

        $builder->add('discountPercent', null, array(
            'label' => "Discount (%):",
            //'disabled' => true,
            'required' => false,
            'attr' => array('class' => 'form-control invoice-discountPercent')
        ));

        $builder->add('footer', null, array(
            'label' => "Footer:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('footer2', null, array(
            'label' => "Footer 2 (In Bold):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control', 'style'=>"font-weight: bold")
        ));

//        $builder->add('footer3', null, array(
//            'label' => "Footer 3:",
//            'required' => false,
//            'attr' => array('class' => 'textarea form-control')
//        ));

        //InvoiceItems
        $builder->add('invoiceItems', CollectionType::class, array(
            'entry_type' => InvoiceItemType::class,
            'entry_options' => array(
                //'data_class' => 'Oleg\TranslationalResearchBundle\Entity\AdminReview',
                'form_custom_value' => $this->params
            ),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__invoiceitems__',
        ));

//        $builder->add('invoiceAddItems', CollectionType::class, array(
//            'entry_type' => InvoiceAddItemType::class,
//            'entry_options' => array(
//                'form_custom_value' => $this->params
//            ),
//            'label' => false,
//            'required' => false,
//            'allow_add' => true,
//            'allow_delete' => true,
//            'by_reference' => false,
//            'prototype' => true,
//            'prototype_name' => '__invoiceadditems__',
//        ));

        //Generated Invoices
//        $builder->add('documents', CollectionType::class, array(
//            'entry_type' => DocumentType::class,
//            'label' => false,
//            'allow_add' => true,
//            'allow_delete' => true,
//            'required' => false,
//            'by_reference' => false,
//            'prototype' => true,
//            'prototype_name' => '__logo__',
//        ));


        $builder->add('subTotal', NumberType::class, array(
            'label' => "Subtotal ($):",
            'scale' => 2,
            'required' => false,
            'attr' => array('class' => 'form-control invoice-subTotal') //'onclick'=>'transresUpdateSubTotal()'
        ));

        $builder->add('total', NumberType::class, array(
            'label' => "Total ($):",
            'scale' => 2,
            'required' => false,
            'attr' => array('class' => 'form-control invoice-total')
        ));

        if( $this->params['cycle'] != 'new' ) {
            $builder->add('paid', NumberType::class, array(
                'label' => "Paid ($):",
                'scale' => 2,
                'required' => false,
                'attr' => array('class' => 'form-control invoice-paid')
            ));
        }

        $builder->add('due', NumberType::class, array(
            'label' => "Balance Due ($):",
            'scale' => 2,
            'required' => false,
            'attr' => array('class' => 'form-control invoice-due', 'readonly'=>'readonly')
        ));

        $builder->add('documents', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'PDF(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));


        //data-toggle="modal" data-target="#exampleModal"
        //"data-toggle"=>"modal", "data-target"=>"#pleaseWaitModal"

        //Buttons
        if( $this->params['cycle'] === "new" ) {
            $builder->add('save', SubmitType::class, array(
                'label' => 'Generate Invoice',
                'attr' => array('class' => 'btn btn-primary btn-with-wait')
            ));
            $builder->add('saveAndGeneratePdf', SubmitType::class, array(
                'label' => 'Save and Generate PDF Invoice',
                'attr' => array('class' => 'btn btn-primary btn-with-wait', "data-toggle"=>"modal", "data-target"=>"#pleaseWaitModal")
            ));
            $builder->add('saveAndGeneratePdfAndSendByEmail', SubmitType::class, array(
                'label' => 'Save, Generate PDF Invoice and Send PDF Invoice by Email to PI',
                'attr' => array('class' => 'btn btn-warning btn-with-wait', "data-toggle"=>"modal", "data-target"=>"#pleaseWaitModal")
            ));
        }
        if( $this->params['invoice']->getLatestVersion() === true ) {
            if( $this->params['cycle'] == "edit" ) {

                if (count($this->params['invoice']->getDocuments()) > 0) {
                    $generatePrefix = "Regenerate";
                } else {
                    $generatePrefix = "Generate";
                }

                $builder->add('edit', SubmitType::class, array(
                    'label' => 'Update Invoice',
                    'attr' => array('class' => 'btn btn-primary btn-with-wait')
                ));
                $builder->add('saveAndGeneratePdf', SubmitType::class, array(
                    'label' => "Save and $generatePrefix PDF Invoice",
                    'attr' => array('class' => 'btn btn-primary btn-with-wait', "data-toggle"=>"modal", "data-target"=>"#pleaseWaitModal") //'onClick'=>"this.disabled=true; this.value = 'Please Wait!';"
                ));
                $builder->add('saveAndGeneratePdfAndSendByEmail', SubmitType::class, array(
                    'label' => "Save, $generatePrefix PDF Invoice and Send PDF Invoice by Email to PI",
                    'attr' => array('class' => 'btn btn-warning btn-with-wait', "data-toggle"=>"modal", "data-target"=>"#pleaseWaitModal")
                ));

//                if (count($this->params['invoice']->getDocuments()) > 0) {
//                    $builder->add('sendByEmail', SubmitType::class, array(
//                        'label' => 'Send the Most Recent Invoice PDF by Email',
//                        'attr' => array('class' => 'btn btn-warning')
//                    ));
//                }

            }

//            if( $this->params['cycle'] == "show" ) {
//                $builder->add('sendByEmail', SubmitType::class, array(
//                    'label' => 'Send the Most Recent Invoice PDF by Email',
//                    'disabled' => false,
//                    'attr' => array('class' => 'btn btn-warning')
//                ));
//            }
        }

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\TranslationalResearchBundle\Entity\Invoice',
            'form_custom_value' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oleg_translationalresearchbundle_invoice';
    }


}
