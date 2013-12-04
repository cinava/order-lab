<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Doctrine\ORM\EntityRepository;

class SpecialStainsType extends AbstractType
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

        $builder->add('field', 'textarea', array(
            'label' => 'Results of Special Stains',
            'required' => false,
            'attr' => array('class'=>'textarea form-control form-control-modif')
        ));

        if($this->params['cicle'] == "" || $this->params['cicle'] == 'new' || $this->params['cicle'] == 'create' ) {
            $attr = array('class' => 'ajax-combobox-staintype', 'type' => 'hidden');    //new
        } else {
            $attr = array('class' => 'combobox combobox-width');    //show
        }
        $builder->add('staintype', 'custom_selector', array(
            'label' => false,
            'required' => true,
            'attr' => $attr,
            'classtype' => 'staintype'
        ));

        $builder->add('stainothers', new ArrayFieldType(), array(
            'data_class' => 'Oleg\OrderformBundle\Entity\SpecialStains',
            'label' => false
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\SpecialStains'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_specialstainstype';
    }
}
