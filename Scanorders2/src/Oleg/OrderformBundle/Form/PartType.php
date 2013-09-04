<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Oleg\OrderformBundle\Helper\FormHelper;

class PartType extends AbstractType
{
    
    protected $multy;
    
    public function __construct( $multy = false )
    {
        $this->multy = $multy;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $helper = new FormHelper();  
        
        if( $this->multy ) {          
            $builder->add('block', 'collection', array(
                'type' => new BlockType($this->multy),
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'label' => "Block:",
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__block__',
            )); 
        }      
        
        $builder->add('name', 'choice', array(        
            'choices' => $helper->getPart(),
            'required'=>true,
            'label'=>'Part Name:',
            'max_length'=>'3',
            'data' => 0,
//            'attr' => array('style' => 'width:70px'),
            'attr' => array('class' => 'combobox', 'style' => 'width:70px', 'required' => 'required', 'disabled')
        ));      
        
//        $builder->add( 'sourceOrgan', 'text', array(
//                'label'=>'Source Organ:', 
//                'max_length'=>'100', 
//                'required'=>false
//        ));
//        $builder->add( 'sourceOrgan',
//                'choice', array(
//                'label'=>'Source Organ:',
//                'max_length'=>'100',
//                'choices' => $helper->getSourceOrgan(),
//                'required'=>false,
//                'attr' => array('class' => 'combobox combobox-width'), // 'style' => 'width:345px'),
////                'attr' => array('class' => 'combobox', 'style' => 'width:70px', 'required' => 'required', 'disabled')
//                //'data' => 0,
//        ));
        $builder->add('sourceOrgan', null, array(
            'label' => 'Source Organ:',
            'attr' => array('class' => 'combobox combobox-width')
        ));
        
        $builder->add( 'description', 'textarea', array(
                'label'=>'Gross Description:',
                'max_length'=>'10000', 
                'required'=>false,
                'attr' => array('class'=>'form-control'),
        ));
        
        $builder->add( 'diagnosis', 'textarea', array(
                'label'=>'Diagnosis:',
                'max_length'=>'10000', 
                'required'=>false,
                'attr' => array('class'=>'form-control'),
        ));
        
        $builder->add( 'diffDiagnosis', 'textarea', array(
                'label'=>'Differential Diagnoses:', 
                'max_length'=>'10000', 
                'required'=>false,
                'attr' => array('class'=>'form-control'),
        ));
        
        $builder->add( 'diseaseType', 'text', array(
                'label'=>'Disease Type:', 
                'max_length'=>'100', 
                'required'=>false,
                'attr' => array('class'=>'form-control form-control-modif'),
        ));
        
//        $builder->add( 'accession', new AccessionType(), array(
//            'label'=>' ',
//            'required'=>false,
//            //'hidden'=>true,
//        ));


        $factory  = $builder->getFormFactory();
        $builder->addEventListener( FormEvents::PRE_SET_DATA, function(FormEvent $event) use($factory){

                $form = $event->getForm();
                $data = $event->getData();

                if( get_parent_class($data) == 'Oleg\OrderformBundle\Entity\Part' || get_class($data) == 'Oleg\OrderformBundle\Entity\Part' ) {

                    $name = $data->getName();
                    $source = $data->getSourceOrgan();

                    $helper = new FormHelper();
                    $arr = $helper->getPart();
                    $sourceArr = $helper->getSourceOrgan();

                    //name
                    $param = array(
                        'choices' => $arr,
                        'required'=>true,
                        'label'=>'Part Name:',
                        'max_length'=>'3',
                        'attr' => array('class' => 'combobox', 'style' => 'width:70px'),
                        'auto_initialize' => false,
                    );

                    $counter = 0;
                    $key = 0;
                    foreach( $arr as $var ){
                        //echo "<br>".$var."?".$name;
                        if( trim( $var ) == trim( $name ) ){
                            $key = $counter;
                            //echo " key=".$key;
                            //$param['data'] = $key;
                            break;
                        }
                        $counter++;
                    }
                    $param['data'] = $key;

                    // field name, field type, data, options
                    $form->add(
                        $factory->createNamed(
                            'name',
                            'choice',
                            null,
                            $param
                        ));

                }

            }
        );

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Part'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_parttype';
    }
}
