<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class PatientType extends AbstractType
{

    protected $params;
    protected $entity;

    public function __construct( $params=null, $entity = null )
    {
        $this->params = $params;
        $this->entity = $entity;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        echo "patient params=";
//        print_r($this->params);
//        echo "<br>";

        $builder->add( 'mrn', 'text', array(
                'label'=>'MRN:',
                'max_length'=>100,
                'required'=>false,
                'attr' => array('class'=>'form-control form-control-modif', 'style' => 'width:250px'),
        ));
        
        $builder->add( 'name', 'text', array(
                'label'=>'Name:', 
                'max_length'=>500,
                'required'=>false,
                'attr' => array('class'=>'form-control form-control-modif'),
        ));
        
        
        $builder->add( 'age', 'text', array(
                'label'=>'Age:', 
                'max_length'=>3,
                'required'=>false,
                'attr' => array('class'=>'form-control form-control-modif'),
        ));               
        
        $builder->add( 'sex', 'choice', array(
                'label'=>'Sex:', 
                'max_length'=>20,
                'required'=>false,
                'choices' => array("Male"=>"Male", "Female"=>"Female"),
                'multiple' => false,
                'expanded' => true,
                'attr' => array('class' => 'horizontal_type')
                //'data' => 'Male',             
        ));
             
        $builder->add( 'dob', 'date', array(
                'label'=>'DOB:',
                'widget' => 'single_text',
                'required'=>false,
                'format' => 'MM-dd-yyyy',
                'attr' => array('class' => 'datepicker'),
        ));
        
//        $builder->add( 'clinicalHistory', 'textarea', array(
//                'label'=>'Clinical History:',
//                'max_length'=>10000,
//                'required'=>false,
//                'attr' => array('class'=>'textarea form-control'),
//        ));
        $builder->add('clinicalHistory', 'collection', array(
            'type' => new ClinicalHistoryType(),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => "Clinical History:",
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__clinicalHistory__',
        ));

        if( $this->params['type'] != 'single' ) {
            $builder->add('specimen', 'collection', array(
                'type' => new SpecimenType($this->params),
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => " ",
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__specimen__',
            ));  
        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Patient'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_patienttype';
    }
}
