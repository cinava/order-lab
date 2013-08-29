<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Oleg\OrderformBundle\Helper\FormHelper;

class OrderInfoType extends AbstractType
{
    
    protected $multy;
    protected $service;
    
    public function __construct( $multy = null, $service = null )
    {
        $this->multy = $multy;
        $this->service = trim($service);
    }
        
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $helper = new FormHelper();

        $builder->add( 'id' , 'hidden' );

        $builder->add( 'type', 'hidden' ); 
        
        if( $this->multy ) {
            $builder->add('patient', 'collection', array(
                'type' => new PatientType($this->multy),
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => " ",
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__patient__',
            ));
        }

        //echo "<br>type=".$this->multy."<br>";

        if( $this->multy == 'educational' ) {
            //echo " add type educational ";
            $builder->add( 'educational', new EducationalType(), array('label'=>'Educational:') );
        }

        if( $this->multy == 'research' ) {
            //echo " add type research ";
            $builder->add( 'research', new ResearchType(), array('label'=>'Research:') );
        }

        $builder->add( 'provider', 'text', array(
                'label'=>'* Ordering Provider:', 
                'max_length'=>'200', 
                'required'=>true,
                'attr' => array('required' => 'required', 'class'=>'form-control form-control-modif')
        ));

        if( 0 ) {//$this->service ) {
            $builder->add( 'pathologyService', 'text', array(
                'label'=>'Pathology Service:',
                'max_length'=>'200',
                'required'=>false,
                'attr' => array('required' => 'required', 'class'=>'form-control form-control-modif')
            ));
        } else {

//            $helper = new FormHelper();
//            $email = $this->get('security.context')->getToken()->getAttribute('email');
//            $service = $helper->getUserPathology($email);
//            $email = 'Gynecologic Pathology / Perinatal Pathology / Autopsy';//'oli2002@med.cornell.edu';

            $pathServices = $helper->getPathologyService();
            $pathParam = array(
                'label' => 'Pathology Service:',
                'max_length'=>200,
                'choices' => $pathServices,
                'required'=>false,
                'attr' => array('class' => 'combobox', 'style' => 'min-width:345px'),
            );

            $counter = 0;
            foreach( $pathServices as $ser ){
                //echo "<br>ser=(".$ser.") (".$this->service.")<br>";
                if( trim($ser) == trim($this->service) ){
                    //echo "found";
                    $key = $counter;
                    //echo " key=".$key;
                    $pathParam['data'] = $key;
                }
                $counter++;
            }

            $builder->add( 'pathologyService', 'choice', $pathParam );

        }
//

        
        $builder->add( 'priority', 'choice', array(
                'label' => '* Priority:', 
                //'max_length'=>200,
                'required' => true,
                'choices' => $helper->getPriority(),
                'data' => 0,    //'Routine',
                'multiple' => false,
                'expanded' => true,
                'attr' => array('class' => 'horizontal_type', 'required' => 'required')
        ));
        
        $builder->add( 'slideDelivery', 
                'choice', array(  
                'label'=>'* Slide Delivery:',
                'max_length'=>200,
                'choices' => $helper->getSlideDelivery(),
                'required'=>true,
                'data' => 0,     
                'attr' => array('class' => 'combobox', 'style' => 'min-width:345px', 'required' => 'required')
        ));
                
        $builder->add( 'returnSlide', 
                'choice', array(
                'label'=>'* Return Slides to:', 
                'max_length'=>200,
                'choices' => $helper->getReturnSlide(),
                'required'=>true,
                'data' => 0,    //'Filing Room',
                'attr' => array('class' => 'combobox', 'style' => 'min-width:345px', 'required' => 'required')
        ));

        $builder->add('scandeadline','date',array(
            'widget' => 'single_text',
            'format' => 'MM-dd-yyyy',
            'attr' => array('class' => 'datepicker'),
            'required' => false,
            'data' => date_modify(new \DateTime(), '+2 week'),
            'label'=>'Scan Deadline:',
        ));
        
        $builder->add('returnoption', 'checkbox', array(
            'label'     => 'Return slide(s) by this date even if not scanned',
            'required'  => false,
        ));


        //fill out choices with pre set data PRE_SET_DATA
        $factory  = $builder->getFormFactory();
        $builder->addEventListener( FormEvents::PRE_SET_DATA, function(FormEvent $event) use($factory){

                $form = $event->getForm();
                $data = $event->getData();

//                echo "class=".get_class($data)."<br>";
//                echo "parent=".get_parent_class($data)."<br>";

                //exit();

                //if( $data instanceof Stain ) {
                //TODO: fix it. Here the listenere always executes this block (because of some preset data on new form?)
                //read: http://symfony.com/doc/current/cookbook/form/dynamic_form_modification.html
                if( get_class($data) == 'Oleg\OrderformBundle\Entity\OrderInfo' ) { //} || get_parent_class($data) == 'Oleg\OrderformBundle\Entity\OrderInfo' ) {

                    $pathservice = $data->getPathologyService();
                    $return = $data->getReturnSlide();
                    $delivery = $data->getSlideDelivery();
                    $priority = $data->getPriority();

                    $helper = new FormHelper();

                    $pathserviceArr = $helper->getPathologyService();
                    $deliveryArr = $helper->getSlideDelivery();
                    $returnArr = $helper->getReturnSlide();
                    $priorityArr = $helper->getPriority();

                    //pathology service
                    $pathservice_param = array(
                        'label' => 'Pathology Service:',
                        'max_length'=>200,
                        'choices' => $pathserviceArr,
                        'required'=>false,
                        'attr' => array('class' => 'combobox', 'style' => 'min-width:345px'),
                        'auto_initialize' => false,
                    );

                    $counter = 0;
                    foreach( $pathserviceArr as $var ){
                        //echo "<br>".$var."?".$pathservice;
                        if( trim( $var ) == trim( $pathservice ) ){
                            $key = $counter;
                            //echo "!!!!!!!!!!!!!!!!!! key=".$key;
                            $pathservice_param['data'] = $key;
                        }
                        $counter++;
                    }

                    // field name, field type, data, options
                    $form->add(
                        $factory->createNamed(
                            'pathologyService',
                            'choice',
                            null,
                            $pathservice_param
                    ));

                    //delivery
                    $delivery_param = array(
                        'label'=>'* Slide Delivery:',
                        'max_length'=>200,
                        'choices' => $deliveryArr,
                        'required'=>true,
                        'attr' => array('class' => 'combobox', 'required' => 'required', 'style' => 'min-width:345px'),
                        'auto_initialize' => false,
                    );

                    $counter = 0;
                    foreach( $deliveryArr as $var ){
                        if( trim( $var ) == trim( $delivery ) ){
                            $key = $counter;
                            $delivery_param['data'] = $key;
                        }
                        $counter++;
                    }

                    // field name, field type, data, options
                    $form->add(
                        $factory->createNamed(
                            'slideDelivery',
                            'choice',
                            null,
                            $delivery_param
                    ));


                    //return
                    $return_param = array(
                        'label'=>'* Return Slides to:',
                        'max_length'=>200,
                        'choices' => $helper->getReturnSlide(),
                        'required'=>true,
                        'attr' => array('class' => 'combobox', 'required' => 'required', 'style' => 'min-width:345px'),
                        'auto_initialize' => false,
                    );

                    $counter = 0;
                    foreach( $returnArr as $var ){
                        if( trim( $var ) == trim( $return ) ){
                            $key = $counter;
                            $return_param['data'] = $key;
                        }
                        $counter++;
                    }

                    // field name, field type, data, options
                    $form->add(
                        $factory->createNamed(
                            'returnSlide',
                            'choice',
                            null,
                            $return_param
                        ));


                    //priority
                    $priority_param = array(
                        'label' => '* Priority:',
                        //'max_length'=>200,
                        'required' => true,
                        'choices' => $priorityArr,
                        'multiple' => false,
                        'expanded' => true,
                        'data' => 0,
                        'attr' => array('class' => 'horizontal_type', 'required' => 'required'),
                        'auto_initialize' => false,
                    );

                    $counter = 0;
                    foreach( $priorityArr as $var ){
                        //echo "<br>".$var."?".$pathservice;
                        if( trim( $var ) == trim( $priority ) ){
                            $key = $counter;
                            $priority_param['data'] = $key;
                        }
                        $counter++;
                    }

                    // field name, field type, data, options
                    $form->add(
                        $factory->createNamed(
                            'priority',
                            'choice',
                            null,
                            $priority_param
                        ));

                    //hidden id
                    //priority
//                    $id_param = array(
//                        'label' => 'ID:',
//                        //'max_length'=>200,
//                        'required' => true,
//                        'data' => $data->getId(),
//                        'auto_initialize' => false,
//                    );
//                    $form->add(
//                        $factory->createNamed(
//                            'id',
//                            'hidden',
//                            null,
//                            $id_param
//                        ));

                }

            }
        );
        
        
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
