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

    protected $params;
    protected $entity;

    public function __construct( $params=null, $entity = null )
    {
        $this->params = $params;
        $this->entity = $entity;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $helper = new FormHelper();  
        
        if( $this->params['type'] != 'single' ) {
            $builder->add('block', 'collection', array(
                'type' => new BlockType($this->params),
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'label' => "Block:",
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__block__',
            )); 
        }

        //name
        $builder->add('partname', 'collection', array(
            'type' => new PartNameType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => "Part Name:",
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__partpartname__',
        ));

        //sourceOrgan
        $builder->add('sourceOrgan', 'collection', array(
            'type' => new PartSourceOrganType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => "Source Organ:",
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__partsourceOrgan__',
        ));

        //description
        $gen_attr = array('label'=>'Gross Description','class'=>'Oleg\OrderformBundle\Entity\PartDescription','type'=>null);    //type=null => auto type
        $builder->add('description', 'collection', array(
            'type' => new GenericFieldType($this->params, null, $gen_attr),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => "Gross Description:",
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__partdescription__',
        ));

        //partdiagnosis
        $gen_attr = array('label'=>'Diagnosis','class'=>'Oleg\OrderformBundle\Entity\PartDiagnos','type'=>null);    //type=null => auto type
        $builder->add('diagnos', 'collection', array(
            'type' => new GenericFieldType($this->params, null, $gen_attr),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => "Diagnosis:",
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__partdiagnos__',
        ));

        //paper
        $builder->add('paper', 'collection', array(
            'type' => new PartPaperType($this->params),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__paper__',
        ));

        //diffDiagnoses
        $builder->add('diffDiagnoses', 'collection', array(
            'type' => new DiffDiagnosesType(),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__diffDiagnoses__',
        ));

        $builder->add( 'diseaseType', 'choice', array(
            'label'=>'Type of Disease:',
            //'required'=>false,
            'choices' => array("Neoplastic"=>"Neoplastic", "Non-Neoplastic"=>"Non-Neoplastic", "None"=>"None"),
            'multiple' => false,
            'expanded' => true,
            'attr' => array('class' => 'horizontal_type'), //'required' => '0', 'disabled'
            //'data' => 'Male',
        ));

        $builder->add( 'origin', 'choice', array(
            'label'=>'Origin:',
            //'required'=>false,
            'choices' => array("Primary"=>"Primary", "Metastatic"=>"Metastatic"),
            'multiple' => false,
            'expanded' => true,
            'attr' => array('class' => 'horizontal_type'),
        ));

        if($this->params['cicle'] == "" || $this->params['cicle'] == 'new' || $this->params['cicle'] == 'create' ) {
            $attr = array('class' => 'ajax-combobox-organ', 'type' => 'hidden');    //new
        } else {
            $attr = array('class' => 'combobox combobox-width');    //show
        }
        $builder->add('primaryOrgan', 'custom_selector', array(
            'label' => 'Primary Site of Origin:',
            'attr' => $attr,
            'required' => false,
            'classtype' => 'sourceOrgan'
        ));


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
