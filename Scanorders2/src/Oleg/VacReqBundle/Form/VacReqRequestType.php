<?php

namespace Oleg\VacReqBundle\Form;


use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Oleg\VacReqBundle\Form\VacReqRequestBusinessType;


class VacReqRequestType extends AbstractType
{

    protected $params;

    public function __construct( $params )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


//        ///////////////////////// tree node /////////////////////////
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $title = $event->getData();
//            $form = $event->getForm();
//
//            $label = null;
//            if( $title ) {
//                $institution = $title->getInstitution();
//                if( $institution ) {
//                    $label = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->getLevelLabels($institution) . ":";
//                }
//            }
//            if( !$label ) {
//                $label = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->getLevelLabels(null) . ":";
//            }
//            //echo "label=".$label."<br>";
//
//            $form->add('institution', 'employees_custom_selector', array(
//                'label' => $label,
//                'required' => false,
//                //'attr' => array('class' => 'ajax-combobox-institution', 'type' => 'hidden'),
//                'attr' => array(
//                    'class' => 'ajax-combobox-compositetree',
//                    'type' => 'hidden',
//                    'data-compositetree-bundlename' => 'UserdirectoryBundle',
//                    'data-compositetree-classname' => 'Institution'
//                ),
//                'classtype' => 'institution'
//            ));
//        });
//        ///////////////////////// EOF tree node /////////////////////////

//        if( $this->params['cycle'] != 'new' ) {
//            $builder->add('status', 'choice', array(
//                'disabled' => ($this->params['roleAdmin'] ? true : false),
//                'choices' => array(
//                    'pending' => 'pending',
//                    'approved' => 'approved',
//                    'rejected' => 'rejected'
//                ),
//                'label' => "Status:",
//                'required' => true,
//                'attr' => array('class' => 'combobox combobox-width'),
//            ));
//        }

        $builder->add('phone', null, array(
            'label' => "Phone Number for the person away:",
            'attr' => array('class' => 'form-control vacreq-phone'),
            'read_only' => ($this->params['review'] ? true : false)
        ));

//        $builder->add('availabilities', null, array(
//            'label' => "Availability:",
//            'attr' => array('class' => 'combobox combobox-width vacreq-availabilities'),
//            'read_only' => ($this->params['review'] ? true : false)
//        ));
//        $builder->add('emergencyCellPhone', null, array(
//            'label' => "Cell Phone:",
//            'attr' => array('class' => 'form-control'),
//        ));
//
//        $builder->add('emergencyOther', null, array(
//            'label' => "Other:",
//            'attr' => array('class' => 'form-control'),
//        ));
//        $builder->add('emergencyComment', null, array(
//            'label' => "Emergency Comment:",
//            'attr' => array('class' => 'form-control vacreq-emergencyComment'),
//            'read_only' => ($this->params['review'] ? true : false)
//        ));

        //Business Travel
        $builder->add('requestBusiness', new VacReqRequestBusinessType($this->params), array(
            'data_class' => 'Oleg\VacReqBundle\Entity\VacReqRequestBusiness',
            'label' => false,
            'required' => false,
        ));

        //Business Travel
        $builder->add('requestVacation', new VacReqRequestVacationType($this->params), array(
            'data_class' => 'Oleg\VacReqBundle\Entity\VacReqRequestVacation',
            'label' => false,
            'required' => false,
        ));

        if( $this->params['cycle'] != 'show' && !$this->params['review'] ) {
//            $builder->add('user', null, array(
//                //'data_class' => 'Oleg\UserdirectoryBundle\Entity\User',
//                'read_only' => ($this->params['roleAdmin'] ? false : true),
//                'label' => "Requester:",
//                'required' => true,
//                'attr' => array('class' => 'combobox combobox-width vacreq-user')
//            ));

            //enabled ($readOnly = false) for admin only
            $readOnly = true;
            if( $this->params['roleAdmin'] ) {
                $readOnly = false;
            }

            $builder->add('submitter', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:User',
                'label' => "Request Submitter:",
                'required' => true,
                'multiple' => false,
                //'property' => 'name',
                'attr' => array('class' => 'combobox combobox-width'),
                'read_only' => true,    //$readOnly,   //($this->params['review'] ? true : false),
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('user')
                        ->leftJoin("user.infos","infos")
                        ->leftJoin("user.employmentStatus", "employmentStatus")
                        ->leftJoin("employmentStatus.employmentType", "employmentType")
                        ->andWhere("user.keytype IS NOT NULL AND user.primaryPublicUserId != 'system'")
                        ->andWhere("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                        ->orderBy("infos.lastName","ASC");
                },
            ));

            $builder->add('user', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:User',
                'label' => "Person Away:",
                'required' => true,
                'multiple' => false,
                //'property' => 'name',
                'attr' => array('class' => 'combobox combobox-width'),
                //'read_only' => $readOnly,   //($this->params['review'] ? true : false),
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('user')
                        ->leftJoin("user.infos","infos")
                        ->leftJoin("user.employmentStatus", "employmentStatus")
                        ->leftJoin("employmentStatus.employmentType", "employmentType")
                        ->andWhere("user.keytype IS NOT NULL AND user.primaryPublicUserId != 'system'")
                        ->andWhere("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                        ->orderBy("infos.lastName","ASC");
                },
            ));
        }

        //add organizational group <-> institution
//        $builder->add('institution', 'choice', array(
//            'data_class' => 'Oleg\UserdirectoryBundle\Entity\Institution',
//            //'class' => 'OlegUserdirectoryBundle:Institution',
//            //'property' => 'getUserNameStr',
//            'label' => "Organizational Group",
//            'required' => false,
//            //'multiple' => false,
//            'attr' => array('class' => 'combobox combobox-width vacreq-institution', 'placeholder' => 'Organizational Group'),
//            'choices' => $this->params['organizationalInstitutions'],
//        ));
//        $builder->add('organizationalInstitutions', 'choice', array(
//            //'class' => 'OlegUserdirectoryBundle:Institution',
//            //'property' => 'getUserNameStr',
//            'mapped' => false,
//            'label' => "Organizational Group",
//            'required' => true,
//            //'read_only' => ($this->params['roleAdmin'] ? false : true),
//            //'multiple' => false,
//            'attr' => array('class' => 'combobox combobox-width vacreq-institution', 'placeholder' => 'Organizational Group'),
//            'choices' => $this->params['organizationalInstitutions'],
//        ));

        $requiredInst = false;
        if( count($this->params['organizationalInstitutions']) == 1 ) {
            //echo "set org inst <br>";
            $requiredInst = true;
        }

        $builder->add('institution', 'choice', array(
            'label' => "Organizational Group:",
            'required' => $requiredInst,
            'attr' => array('class' => 'combobox combobox-width vacreq-institution', 'placeholder' => 'Organizational Group'),
            'choices' => $this->params['organizationalInstitutions'],
            'read_only' => ($this->params['review'] ? true : false)
        ));
        $builder->get('institution')
            ->addModelTransformer(new CallbackTransformer(
                //original from DB to form: institutionObject to institutionId
                function($originalInstitution) {
                    //echo "originalInstitution=".$originalInstitution."<br>";
                    if( is_object($originalInstitution) && $originalInstitution->getId() ) { //object
                        return $originalInstitution->getId();
                    }
                    return $originalInstitution; //id
                },
                //reverse from form to DB: institutionId to institutionObject
                function($submittedInstitutionObject) {
                    //echo "submittedInstitutionObject=".$submittedInstitutionObject."<br>";
                    if( $submittedInstitutionObject ) { //id
                        $institutionObject = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->find($submittedInstitutionObject);
                        return $institutionObject;
                    }
                    return null;
                }
            )
        );

//        $builder->add('approver', null, array(
//            //'data_class' => 'Oleg\UserdirectoryBundle\Entity\User',
//            'label' => "Approver:",
//            'required' => false,
//            'attr' => array('class' => 'combobox combobox-width vacreq-approver')
//        ));

        $builder->add('availableViaEmail', null, array(
            'label' => "Available via E-Mail:",
            'attr' => array('class' => 'vacreq-availableViaEmail'),
            'read_only' => ($this->params['review'] ? true : false)
        ));
        $builder->add('availableEmail', null, array(
            'label' => "E-Mail address while away on this trip:",
            'attr' => array('class' => 'form-control vacreq-availableEmail'),
            'read_only' => ($this->params['review'] ? true : false)
        ));

        $builder->add('availableViaCellPhone', null, array(
            'label' => "Available via Cell Phone:",
            'attr' => array('class' => 'vacreq-availableViaCellPhone'),
            'read_only' => ($this->params['review'] ? true : false)
        ));
        $builder->add('availableCellPhone', null, array(
            'label' => "Cell Phone number while away on this trip:",
            'attr' => array('class' => 'form-control vacreq-availableCellPhone'),
            'read_only' => ($this->params['review'] ? true : false)
        ));

        $builder->add('availableViaOther', null, array(
            'label' => "Available via another method:",
            'attr' => array('class' => 'vacreq-availableViaOther'),
            'read_only' => ($this->params['review'] ? true : false)
        ));
        $builder->add('availableOther', null, array(
            'label' => "Other:",
            'attr' => array('class' => 'form-control vacreq-availableOther'),
            'read_only' => ($this->params['review'] ? true : false)
        ));

        $builder->add('availableNone', null, array(
            'label' => "Not Available:",
            'attr' => array('class' => 'vacreq-availableNone'),
            'read_only' => ($this->params['review'] ? true : false)
        ));


        $builder->add('firstDayBackInOffice', 'date', array(
            'label' => 'First Day Back in Office:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control vacreq-firstDayBackInOffice'),
            'read_only' => ($this->params['review'] ? true : false)
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\VacReqBundle\Entity\VacReqRequest',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'oleg_vacreqbundle_request';
    }
}
