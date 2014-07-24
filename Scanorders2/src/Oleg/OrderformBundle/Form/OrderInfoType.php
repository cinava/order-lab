<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Oleg\OrderformBundle\Helper\FormHelper;
use Doctrine\ORM\EntityRepository;

class OrderInfoType extends AbstractType
{

    protected $entity;
    protected $params;
    
//    public function __construct( $type = null, $service = null, $entity = null )
    //params: type: single or clinical, educational, research
    //params: cicle: new, edit, show
    //params: service: pathology service
    //params: entity: entity itself
    public function __construct( $params=null, $entity=null )
    {
        if( $params ) $this->params = $params;
        if( $entity ) $this->entity = $entity;
    }
        
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        echo "orderinfo params=";
        //echo "type=".$this->params['type']."<br>";
        //echo "cicle=".$this->params['cicle']."<br>";
//        echo "<br>";

        $helper = new FormHelper();

        $builder->add( 'oid' , 'hidden', array('attr'=>array('class'=>'orderinfo-id')) );

        //unmapped data quality form to record the MRN-Accession conflicts
        $builder->add('conflicts', 'collection', array(
            'mapped' => false,
            'type' => new DataQualityType($this->params, null),
            'label' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__dataquality__',
        ));

        //add children
        //if( $this->params['type'] != 'Table-View Scan Order' || ($this->params['type'] == 'Table-View Scan Order' && $this->params['cicle'] != 'new') ) {
        if( $this->params['type'] != 'Table-View Scan Order' ) {
        //if( $this->params['cicle'] == 'new' ) {

            //echo "orderinfo type: show patient <br>";
            $builder->add('patient', 'collection', array(
                'type' => new PatientType($this->params,$this->entity),    //$this->type),
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__patient__',
            ));

        } else {

            //echo "orderinfo type: show datalocker <br>";

            $builder->add('datalocker','hidden', array(
                "mapped" => false
            ));

            $builder->add('clickedbtn','hidden', array(
                "mapped" => false
            ));

        }

        //echo "<br>type=".$this->type."<br>";

        $builder->add( 'educational', new EducationalType($this->params,$this->entity), array('label'=>'Educational:') );

        $builder->add( 'research', new ResearchType($this->params,$this->entity), array('label'=>'Research:') );

        $attr = array('class' => 'ajax-combobox-pathservice', 'type' => 'hidden');
        $builder->add('pathologyService', 'custom_selector', array(
            'label' => 'Service:',
            'attr' => $attr,
            'required' => false,
            'classtype' => 'pathologyService'
        ));

        //priority
        $priorityArr = array(
            'label' => '* Priority:',
            'choices' => $helper->getPriority(),
            'required' => true,
            'multiple' => false,
            'expanded' => true,
            'attr' => array('class' => 'horizontal_type', 'required'=>'required')
        );
        if($this->params['cicle'] == "" || $this->params['cicle'] == 'new' || $this->params['cicle'] == 'create' ) {
            $priorityArr['data'] = 'Routine';    //new
        }
        $builder->add( 'priority', 'choice', $priorityArr);

        //slideDelivery
        $attr = array('class' => 'ajax-combobox-delivery', 'type' => 'hidden');
        $builder->add('slideDelivery', 'custom_selector', array(
            'label' => '* Slide Delivery:',           
            'attr' => $attr,
            'required'=>true,
            'classtype' => 'slideDelivery'
        ));

        $attr = array('class' => 'ajax-combobox-return', 'type' => 'hidden');
        $builder->add('returnSlide', 'custom_selector', array(
            'label' => '* Return Slides to:',           
            'attr' => $attr,
            'required'=>true,
            'classtype' => 'returnSlide'
        ));

        //scandeadline
        if( $this->params['cicle'] == 'new' ) {
            $scandeadline = date_modify(new \DateTime(), '+2 week');
        } else {
            $scandeadline = null;
        }

        if( $this->entity && $this->entity->getScandeadline() != '' ) {
            $scandeadline = $this->entity->getScandeadline();
        }

        $builder->add('scandeadline','date',array(
            'widget' => 'single_text',
            'format' => 'MM-dd-yyyy',   //used for day dateline (no hours), so we don't need to set view_timezone
            'attr' => array('class' => 'datepicker form-control scandeadline-mask', 'style'=>'margin-top: 0;'),
            'required' => false,
            'data' => $scandeadline,
            'label'=>'Scan Deadline:',
        ));
        
        $builder->add('returnoption', 'checkbox', array(
            'label'     => 'Return slide(s) by this date even if not scanned:',
            'required'  => false,
        ));

//        $builder->add('provider', null, array(
//            'label'=>'* Submitter:',
//            'required' => true,
//            'attr' => array('class' => 'form-control')
//        ));
        $builder->add( 'provider', new ProviderType(), array('label'=>'Submitter:') );

        $builder->add('proxyuser', 'entity', array(
            'class' => 'OlegOrderformBundle:User',
            'label'=>'Ordering Provider:',
            'required' => false,
            //'multiple' => true,
            'attr' => array('class' => 'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.roles LIKE :roles OR u=:user')
                    ->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user'] ));
            },
        ));


        //new fields
        $attr = array('class' => 'ajax-combobox-department', 'type' => 'hidden');
        $builder->add('department', 'custom_selector', array(
            'label' => 'Department:',
            'attr' => $attr,
            'required' => false,
            'classtype' => 'department'
        ));

//        $attr = array('class' => 'ajax-combobox-institution', 'type' => 'hidden');
//        $builder->add('institution', 'custom_selector', array(
//            'label' => 'Institution:',
//            'attr' => $attr,
//            'required' => false,
//            'classtype' => 'institution'
//        ));
//        $instArr = array();
//        foreach( $this->params['user']->getInstitution() as $inst ) {
//            $instArr[$inst->getId()] = $inst->getName();
//        }
//        $builder->add( 'institution', 'choice', array(
//            'label'=>'Institution:',
//            'required' => true,
//            'choices' => $instArr,
//            'multiple' => false,
//            'attr' => array('class' => 'combobox combobox-width')
//        ));
        $builder->add( 'institution', 'entity', array(
            'class' => 'OlegOrderformBundle:Institution',
            'property' => 'name',
            'label'=>'Institution:',
            'required'=> true,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width combobox-institution'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('i')
                    ->innerJoin('i.users', 'user')
                    ->where('user = :user')
                    ->setParameters( array(
                        'user' => $this->params['user'],
                ));
            },
        ));


        $builder->add( 'purpose', 'choice', array(
            'label'=>'Purpose:',
            'required' => true,
            'choices' => array("For Internal Use by WCMC Department of Pathology"=>"For Internal Use by WCMC Department of Pathology", "For External Use (Invoice Fund Number)"=>"For External Use (Invoice Fund Number)"),
            'multiple' => false,
            'expanded' => true,
            'attr' => array('class' => 'horizontal_type')
        ));

        $attr = array('class' => 'ajax-combobox-account', 'type' => 'hidden');
        $builder->add('account', 'custom_selector', array(
            'label' => 'Debit Fund WBS Account Number:',
            'attr' => $attr,
            'required' => false,
            'classtype' => 'account'
        ));
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\OrderInfo'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_orderinfotype';
    }
}
