<?php

namespace Oleg\CallLogBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Oleg\UserdirectoryBundle\Form\InstitutionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;



//This form type is used strictly only for scan order: message (message) form has scan order
//This form includes patient hierarchy form.
//Originally it was made the way that message has scanorder.
//All other order's form should have aggregated message type form: order form has message form.
class CalllogMessageType extends AbstractType
{

    protected $entity;
    protected $params;
    
//    public function __construct( $type = null, $service = null, $entity = null )
    //params: type: single or clinical, educational, research
    //params: cycle: new, edit, show
    //params: service: pathology service
    //params: entity: entity itself
    public function __construct( $params=null, $entity=null )
    {
        if( $params ) $this->params = $params;
        if( $entity ) $this->entity = $entity;

        if( !array_key_exists('type', $this->params) ) {
            $this->params['type'] = 'Unknown Order';
        }

        if( !array_key_exists('message.proxyuser.label', $this->params) ) {
            $this->params['message.proxyuser.label'] = 'Ordering Provider(s):';
        }

    }
        
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        echo "message params=";
        //echo "type=".$this->params['type']."<br>";
        //echo "cycle=".$this->params['cycle']."<br>";
//        echo "<br>";

        $builder->add( 'oid' , 'hidden', array('attr'=>array('class'=>'message-id')) );


        $patient = $this->entity->getPatient()->first();
        //echo "calllog patient id=".$patient->getId()."<br>";

        //echo "message type: show patient <br>";
        $builder->add('patient', 'collection', array(
            'type' => new PatientType($this->params,$patient),    //$this->type),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__patient__',
        ));


//        $builder->add('messageCategory', 'entity', array(
//            'label' => 'Message Type:',
//            //'property' => 'getNodeNameWithRoot',
//            'required' => true,
//            'multiple' => false,
//            'empty_value' => false,
//            'class' => 'OlegOrderformBundle:MessageCategory',
//            'attr' => array('class' => 'combobox combobox-width combobox-messageCategory')
//        ));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $message = $event->getData();
            $form = $event->getForm();

            $label = null;
            $mapper = array(
                'prefix' => "Oleg",
                'className' => "MessageCategory",
                'bundleName' => "OrderformBundle",
                'organizationalGroupType' => "MessageTypeClassifiers"
            );
            if( $message ) {
                $messageCategory = $message->getMessageCategory();
                if( $messageCategory ) {
                    $label = $this->params['em']->getRepository('OlegOrderformBundle:MessageCategory')->getLevelLabels($messageCategory,$mapper);
                }
            }
            if( !$label ) {
                $label = $this->params['em']->getRepository('OlegOrderformBundle:MessageCategory')->getLevelLabels(null,$mapper) . ":";
            }

            //echo "show defaultInstitution label=".$label."<br>";

            $form->add('messageCategory', 'employees_custom_selector', array(
                'label' => $label,
                'required' => false,
                //'read_only' => true,
                'attr' => array(
                    'class' => 'ajax-combobox-compositetree combobox-compositetree-readonly-parent',
                    'type' => 'hidden',
                    'data-compositetree-bundlename' => 'OrderformBundle',
                    'data-compositetree-classname' => 'MessageCategory',
                    'data-label-prefix' => '',
                    'data-readonly-parent-level' => '2' //readonly all children from level 2 up (including this level)
                ),
                'classtype' => 'institution'
            ));
        });


        $builder->add('version', null, array(
            'label' => 'Message Version:',
            'read_only' => true,
            'required' => true,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('amendmentReason', 'custom_selector', array(
            'label' => 'Amendment Reason:',
            'required' => false,
            'attr' => array('class' => 'ajax-combobox-amendmentReason', 'type' => 'hidden'),
            'classtype' => 'amendmentReason'
        ));

        $builder->add('addPatientToList', 'checkbox', array(
            'label' => 'Add patient to the list:',
            'mapped' => false,
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('patientListTitle', 'entity', array(
            'label' => 'List Title:',
            'mapped' => false,
            //'required' => false,
            'property' => 'name',
            //'data' => false,
            'class' => 'OlegOrderformBundle:PatientListHierarchy',
            'attr' => array('class' => 'combobox combobox-width')
        ));


        //Institutional PHI Scope
        if( 0 ) {
            if (array_key_exists('institutions', $this->params)) {
                $institutions = $this->params['institutions'];
            } else {
                $institutions = null;
            }
            //foreach( $institutions as $inst ) {
            //    echo "form inst=".$inst."<br>";
            //}
            $builder->add('institution', 'entity', array(
                'label' => 'Order data visible to members of (Institutional PHI Scope):',
                'property' => 'getNodeNameWithRoot',
                'required' => true,
                'multiple' => false,
                'empty_value' => false,
                'class' => 'OlegUserdirectoryBundle:Institution',
                'choices' => $institutions,
                'attr' => array('class' => 'combobox combobox-width combobox-institution')
            ));
        }


        ////////////////////////// Specific Orders //////////////////////////


//        $builder->add('laborder', new LabOrderType($this->params), array(
//            'data_class' => 'Oleg\OrderformBundle\Entity\LabOrder',
//            'label' => false
//        ));

        ////////////////////////// EOF Specific Orders //////////////////////////
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Message'
        ));
    }

    public function getName()
    {
        return 'oleg_calllogformbundle_messagetype';
    }

}
