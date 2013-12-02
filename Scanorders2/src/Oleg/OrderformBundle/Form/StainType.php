<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Oleg\OrderformBundle\Helper\FormHelper;
use Oleg\OrderformBundle\Form\DataTransformer\StainTransformer;

class StainType extends AbstractType
{

    protected $params;
    protected $user;

    public function __construct( $params=null, $entity = null )
    {
        $this->params = $params;
        $this->entity = $entity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if($this->params['cicle'] == "" || $this->params['cicle'] == 'new' || $this->params['cicle'] == 'create' ) {
            $attr = array('class' => 'ajax-combobox-stain', 'type' => 'hidden');    //new
        } else {
            $attr = array('class' => 'combobox combobox-width');    //show
        }
        $builder->add('field', 'custom_selector', array(
            'label' => '* Stain:',
            'required' => true,
            'attr' => $attr,
            'classtype' => 'stain'
        ));

        $builder->add('stainothers', new ArrayFieldType(), array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Stain',
            'label' => false
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Stain'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_staintype';
    }
}
