<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PatientDobType extends AbstractType
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

        $builder->add('field', 'date', array(
            'label' => "DOB:",
            //'input'  => 'datetime',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',   //used for birth day only (no hours), so we don't need to set view_timezone
            'attr' => array('class' => 'datepicker form-control patient-dob-date'), //, 'style'=>'margin-top: 0;'
        ));

        $builder->add('others', new ArrayFieldType(), array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PatientDob',
            'label' => false,
			'attr' => array('style'=>'display:none;')
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PatientDob',
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_dobtype';
    }
}
