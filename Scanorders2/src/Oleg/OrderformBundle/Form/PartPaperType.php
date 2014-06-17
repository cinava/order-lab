<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oleg\OrderformBundle\Helper\FormHelper;

class PartPaperType extends AbstractType
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

        //echo "cicile=".$this->params['cicle']."<br>";

        if( $this->params['cicle'] == 'new' || $this->params['cicle'] == 'create' || $this->params['cicle'] == 'edit' || $this->params['cicle'] == 'amend' ) {

            //echo " => new, create or edit set file ";
            $builder->add('field', 'file', array(
                'label'=>'Relevant Paper or Abstract',
                'required'=>false,
                //'attr'=>array('class'=>'form-control', 'style'=>'height: 50px'),
                //'data'=>'form.docx'
            ));

        } else {

            //echo "show set name ";
            $builder->add('name', 'text', array(
                'label'=>'Relevant Paper or Abstract',
                'required'=>false,
                'attr'=>array('class'=>'form-control')
            ));

        }

        $builder->add('partothers', new ArrayFieldType(), array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PartPaper',
            'label' => false,
			'attr' => array('style'=>'display:none;')
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PartPaper',
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_partpapertype';
    }
}
