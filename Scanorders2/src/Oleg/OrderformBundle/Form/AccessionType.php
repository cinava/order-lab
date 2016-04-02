<?php

namespace Oleg\OrderformBundle\Form;

use Oleg\UserdirectoryBundle\Form\AttachmentContainerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccessionType extends AbstractType
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

        $builder->add('accessionDate', 'collection', array(
            'type' => new AccessionDateType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__accessionaccessiondate__',
        ));

        $builder->add('accession', 'collection', array(
            'type' => new AccessionAccessionType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__accessionaccession__',
        ));

        $builder->add('part', 'collection', array(
            'type' => new PartType($this->params),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => "Part:",
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__part__',
        ));


        //extra data-structure fields
        if( array_key_exists('datastructure',$this->params) && $this->params['datastructure'] == 'datastructure' ) {
            //echo "accession flag datastructure=".$this->params['datastructure']."<br>";
            $params = array('labelPrefix'=>'Autopsy Image');
            $equipmentTypes = array('Autopsy Camera');
            $params['device.types'] = $equipmentTypes;
            $builder->add('attachmentContainer', new AttachmentContainerType($params), array(
                'required' => false,
                'label' => false
            ));
        }

        //messages
        if( array_key_exists('datastructure',$this->params) && $this->params['datastructure'] == 'datastructure' ) {
            $builder->add('message', 'collection', array(
                'type' => new MessageObjectType($this->params),
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'label' => false,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__accessionmessage__',
            ));
        }
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Accession'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_accessiontype';
    }
}
