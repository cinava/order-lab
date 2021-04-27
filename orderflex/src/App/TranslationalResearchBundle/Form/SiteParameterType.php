<?php

namespace App\TranslationalResearchBundle\Form;

use Doctrine\ORM\EntityRepository;
use App\UserdirectoryBundle\Form\DocumentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteParameterType extends AbstractType
{

    protected $params;

    public function formConstructor( $params ) {
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value']);

        $builder->add('transresFromHeader', null, array(
            'label' => "Invoice 'From' Address:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresFooter', null, array(
            'label' => "Invoice Footer:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresNotificationEmail', null, array(
            'label' => "Email Notification Body when Invoice PDF is sent to PI:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresNotificationEmailSubject', null, array(
            'label' => "Email Notification Subject when Invoice PDF is sent to PI:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('invoiceSalesperson', EntityType::class, array(
            'class' => 'AppUserdirectoryBundle:User',
            'label' => "Invoice Salesperson:",
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

        ////////// Invoice reminder email ////////////
        $builder->add('invoiceReminderEmail', null, array(
            'label' => "Reminder Email - Send From the Following Address:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('invoiceReminderSchedule', null, array(
            'label' => "Unpaid Invoice Reminder Schedule 
            (overdue in months,reminder interval in months,max reminder count. 
            For example, '6,3,5' will send reminder emails after 6 months overdue every 3 months for 5 times):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('invoiceReminderSubject', null, array(
            'label' => "Unpaid Invoice Reminder Email Subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('invoiceReminderBody', null, array(
            'label' => "Unpaid Invoice Reminder Email Body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        ////////// EOF Invoice reminder email ////////////

        $builder->add('transresLogos', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Invoice Logo(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        $builder->add('requestCompletedNotifiedEmail', null, array(
            'label' => "Email Notification Body to the Request's PI when Request status is changed to 'Completed and Notified':",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('requestCompletedNotifiedEmailSubject', null, array(
            'label' => "Email Notification Subject to the Request's PI when Request status is changed to 'Completed and Notified':",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('accessionType', EntityType::class, array(
            'class' => 'AppOrderformBundle:AccessionType',
            'label' => "Default Source System for Work Request Deliverables:",
            'required' => false,
            'multiple' => false,
            'choice_label' => 'getOptimalName',
            'attr' => array('class' => 'combobox combobox-width'),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('list')
                    ->where("list.type = :typedef OR list.type = :typeadd")
                    ->orderBy("list.orderinlist","ASC")
                    ->setParameters( array(
                        'typedef' => 'default',
                        'typeadd' => 'user-added',
                    ));
            },
        ));

        //Packing Slip
        $builder->add('transresPackingSlipLogos', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Packing Slip Logo(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        $builder->add('transresPackingSlipTitle', null, array(
            'label' => "Title (i.e. 'Packing Slip'):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipHeadline1', null, array(
            'label' => "Heading Line 1 (i.e. 'Department of Pathology and Laboratory Medicine'):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipHeadline2', null, array(
            'label' => "Heading Line 2 (i.e. 'Translational Research Program'):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipHeadlineColor', null, array(
            'label' => "Heading Font Color (Blue #1E90FF, HTML color value):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipHighlightedColor', null, array(
            'label' => "Heading Font Color (Red #FF0000, HTML color value):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipSubHeading1', null, array(
            'label' => "Sub-heading 1 (i.e. 'COMMENT FOR REQUEST'):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipSubHeading2', null, array(
            'label' => "Sub-heading 2 (i.e. 'LIST OF DELIVERABLE(S)'):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipFooter1', null, array(
            'label' => "Footer Line 1 (i.e. 'Please contact us for more information about this slip.'):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('transresPackingSlipFooter2', null, array(
            'label' => "Footer Line 2 (i.e. 'Translational Research Program * 1300 York Ave., F512, New York, NY 10065 * Tel (212) 746-62255'):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('barcodeSize', null, array(
            'label' => "Packing Slip Barcode size (i.e. 54px):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('transresPackingSlipFontSize', null, array(
            'label' => "Packing Slip Font size (i.e. 14px):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('specimenDetailsComment', null, array(
            'label' => "Specimen Details Comment (The answers you provide must reflect what has been requested in the approved ".$this->params['humanName']." and the approved tissue request form.):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        $builder->add('institutionName', null, array(
            'label' => "Institution Name (i.e. NYP/WCM):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('fromEmail', null, array(
            'label' => "Emails sent by this site will appear to come from the following address (trp-admin@med.cornell.edu):",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        $builder->add('notifyEmail', null, array(
            'label' => 'Cc for email notification when Work Request\' status change to "Completed" and "Completed and Notified" (trp@med.cornell.edu):',
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('showMessageToUsers', null, array(
            'label' => 'Show TRP Message to Users:',
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('messageToUsers', null, array(
            'label' => 'TRP Message to Users:',
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('humanTissueFormNote', null, array(
            'label' => 'Human Tissue Form Note to Users:',
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        //Disable/Enable new project
        $builder->add('enableNewProjectOnSelector', null, array(
            'label' => 'Enable the display the button (project specialty) on the "New Project Request" page (translational-research/project/new):',
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('enableNewProjectOnNavbar', null, array(
            'label' => 'Enable the display the "New Project Request" link in the top Navbar:',
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('enableNewProjectAccessPage', null, array(
            'label' => 'Enable access the "New Project Request" page URL (this is for users who might bookmark this page and try to return to it):',
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('emailNoteConcern', null, array(
            'label' => "Email Notification Asking To Contact With Concerns:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        //Buttons
        if( $this->params['cycle'] === "new" ) {
            $builder->add('save', SubmitType::class, array(
                'label' => 'Save',
                'attr' => array('class' => 'btn btn-warning')
            ));
        }
        if( $this->params['cycle'] === "edit" ) {
            $builder->add('edit', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-warning')
            ));
        }



        ////////// Project reminder email ////////////
        $builder->add('projectReminderDelay_irb_review', null, array(
            'label' => "Pending project request reminder email delay for IRB review (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('projectReminderDelay_admin_review', null, array(
            'label' => "Pending project request reminder email delay for Admin review (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('projectReminderDelay_committee_review', null, array(
            'label' => "Pending project request reminder email delay for Committee review (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('projectReminderDelay_final_review', null, array(
            'label' => "Pending project request reminder email delay for Final review (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('projectReminderDelay_irb_missinginfo', null, array(
            'label' => "Pending project request reminder email delay for IRB Missing Info (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('projectReminderDelay_admin_missinginfo', null, array(
            'label' => "Pending project request reminder email delay for Admin Missing Info (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('projectReminderSubject_review', null, array(
            'label' => "Project request review reminder email subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        $builder->add('projectReminderBody_review', null, array(
            'label' => "Project request review reminder email body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('projectReminderSubject_missinginfo', null, array(
            'label' => "Project request reminder missing info email subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        $builder->add('projectReminderBody_missinginfo', null, array(
            'label' => "Project request reminder missing info email body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        ////////// EOF Project reminder email ////////////

        ////////// Pending work request reminder email ////////////
        $builder->add('pendingRequestReminderDelay', null, array(
            'label' => "Delayed pending work request reminder email delay (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('pendingRequestReminderSubject', null, array(
            'label' => "Delayed pending work request reminder email subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        $builder->add('pendingRequestReminderBody', null, array(
            'label' => "Delayed pending work request reminder email body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        ////////// EOF Pending work request reminder email ////////////

        ////////// Completed work request reminder email ////////////
        $builder->add('completedRequestReminderDelay', null, array(
            'label' => "Delayed completed work request reminder email delay (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('completedRequestReminderSubject', null, array(
            'label' => "Delayed completed work request reminder email subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        $builder->add('completedRequestReminderBody', null, array(
            'label' => "Delayed completed work request reminder email body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        ////////// EOF Completed work request reminder email ////////////

        ////////// Completed and Notified, without issued invoice work request reminder email ////////////
        $builder->add('completedNoInvoiceRequestReminderDelay', null, array(
            'label' => "Delayed completed and notified, without issued invoices work request reminder email delay (in days):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('completedNoInvoiceRequestReminderSubject', null, array(
            'label' => "Delayed completed and notified, without issued invoices work request reminder email subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        $builder->add('completedNoInvoiceRequestReminderBody', null, array(
            'label' => "Delayed completed and notified, without issued invoices work request reminder email body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));
        ////////// EOF Completed and Notified, without issued invoice work request reminder email ////////////

        $builder->add('showRemittance', null, array(
            'label' => "Show Remittance section in invoice PDF:",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('updateProjectFundNumber', null, array(
            'label' => "Update parent Project Request’s Fund Number when New Work request’s number is submitted:",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('transresIntakeForms', CollectionType::class, array(
            'entry_type' => DocumentType::class,
            'label' => 'Intake Form(s):',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__documentsid__',
        ));

        ////////////// Budget Related Parameters /////////////////////
        $builder->add('overBudgetFromEmail', null, array(
            'label' => "Over budget notification from (trp-admin@med.cornell.edu):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('overBudgetSubject', null, array(
            'label' => "Over budget notification subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('overBudgetBody', null, array(
            'label' => "Over budget notification body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('overBudgetSendEmail', null, array(
            'label' => "Send over budget notifications (yes/no):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('approvedBudgetSendEmail', null, array(
            'label' => "Send 'approved project budget' update notifications (yes/no):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('approvedBudgetFromEmail', null, array(
            'label' => "Approved budget amount update notification from (trp-admin@med.cornell.edu):",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('approvedBudgetSubject', null, array(
            'label' => "Approved budget amount update notification email subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('approvedBudgetBody', null, array(
            'label' => "Approved budget update notification email body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('budgetLimitRemovalSubject', null, array(
            'label' => "Approved budget limit removal notification email subject:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('budgetLimitRemovalBody', null, array(
            'label' => "Approved budget limit removal notification email body:",
            'required' => false,
            'attr' => array('class' => 'textarea form-control')
        ));

        $builder->add('overBudgetCalculation', null, array(
            'label' => "Base the notification regarding exceeding the budget on whether the following value exceeds the project budget [Total (Charge and Subsidy) / Charge (without Subsidy)]:",
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        ////////////// EOF Budget Related Parameters /////////////////////

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\TranslationalResearchBundle\Entity\TransResSiteParameters',
            'form_custom_value' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oleg_translationalresearchbundle_siteparameter';
    }


}
