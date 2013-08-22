<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oleg\OrderformBundle\Helper\FormHelper;

class ScanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $helper = new FormHelper();
        
        //$builder->add( 'status', 'hidden', array('data' => 'submitted') ); 
        
        $builder->add('scanregion', 'text', array(
                'max_length'=>200,
                'required'=>false,
                'label' => 'Region to scan'
        ));
        
        $builder->add( 'mag', 
                'choice', array(  
                'label' => 'Magnification:',
                'max_length'=>50,
                'choices' => $helper->getMags(),
                'required' => true,
                'data' => 0,//'20X',
                'multiple' => false,
                'expanded' => true,
                'attr' => array('class' => 'horizontal_type', 'required' => 'required', 'title'=>'40X Scan Batch is run Fri-Mon. Some slide may have to be rescanned once or more. We will do our best to expedite the scanning.')
        ));                       
        
        $builder->add('note', 'textarea', array(
                'max_length'=>5000,
                'required'=>false,
                'label'=>'Reason for Scan/Note:',
                'data' => 'Interesting case',
                //'attr'=>array('readonly'=>true)
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Scan'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_scantype';
    }
}
