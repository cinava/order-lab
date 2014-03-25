<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class ResearchType extends AbstractType
{

    protected $entity;
    protected $params;

    public function __construct( $params=null, $entity=null )
    {
        if( $params ) $this->params = $params;
        if( $entity ) $this->entity = $entity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {     
//        $helper = new FormHelper();
        
        $builder->add( 'project', 'text', array(
                'label'=>'Research Project Title:',
                'max_length'=>'500',
                'required'=> false,
                'attr' => array('class'=>'form-control form-control-modif'),
        ));

        $builder->add( 'settitle', 'text', array(
            'label'=>'Research Set Title:',
            'max_length'=>'500',
            'required'=> false,
            'attr' => array('class'=>'form-control form-control-modif'),
        ));

        if( $this->params['type'] == 'SingleObject' ) {
            $attr = array('class'=>'form-control form-control-modif');
        } else {
            $attr = array('class' => 'ajax-combobox-optionaluser-research', 'type' => 'hidden');
        }

        $builder->add('principalstr', 'custom_selector', array(
            'label' => 'Principal Investigator:',
            'attr' => $attr,
            'required'=>false,
            'classtype' => 'optionalUserResearch'
        ));


        if( $this->params['type'] == 'SingleObject' ) {

            $attr = array('class' => 'combobox combobox-width');
            $builder->add('principal', 'entity', array(
                'class' => 'OlegOrderformBundle:User',
                'label'=>'Principal:',
                'required' => false,
                //'read_only' => true,    //not working => disable by twig
                //'multiple' => true,
                'attr' => $attr,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.locked=:locked')
                        ->setParameter('locked', '0');
                },
            ));
        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Research'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_researchtype';
    }
}
