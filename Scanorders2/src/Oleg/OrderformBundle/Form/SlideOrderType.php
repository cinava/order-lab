<?php

namespace Oleg\OrderformBundle\Form;

use Oleg\UserdirectoryBundle\Form\DocumentContainerType;
use Oleg\UserdirectoryBundle\Form\DocumentType;
use Oleg\UserdirectoryBundle\Form\UserWrapperType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SlideOrderType extends AbstractType
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

        $builder->add('processedDate', 'date', array(
            'label' => "Slide Cut or Prepared On:",
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM-dd-yyyy',
            'attr' => array('class' => 'datepicker form-control patientdob-mask'),
        ));

        $builder->add('processedByUser', null, array(
            'label' => 'Slide Cut or Prepared By:',
            'attr' => array('class' => 'combobox combobox-width'),
        ));


        $params = array('labelPrefix'=>' for Slide Cutter');
        $builder->add('instruction', new InstructionType($params), array(
            'data_class' => 'Oleg\OrderformBundle\Entity\InstructionList',
            'label' => false
        ));




    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\SlideOrder',
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_slideordertype';
    }
}
