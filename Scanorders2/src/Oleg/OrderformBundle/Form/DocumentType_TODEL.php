<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Oleg\OrderformBundle\Helper\FormHelper;

class DocumentType_TODEL extends AbstractType
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


        //echo "cycle=".$this->params['cycle'];
        if( $this->params['cycle'] == 'new' || $this->params['cycle'] == 'create' || $this->params['cycle'] == 'edit' || $this->params['cycle'] == 'amend' ) {

            //echo " => new, create or edit set file ";
            $builder->add('file', 'file', array(
                'label'=>'Relevant Paper or Abstract',
                'required'=>false,
            ));

        } else {

            //echo "show set name ";
            $builder->add('name', 'text', array(
                'label'=>'Relevant Paper or Abstract',
                'required'=>false,
            ));

        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Document'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_documenttype';
    }
}
