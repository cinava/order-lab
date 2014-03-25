<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

//use Oleg\OrderformBundle\Helper\FormHelper;

class EducationalType extends AbstractType
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

        $builder->add( 'course', 'text', array(
            'label'=>'Course Title:',
            'max_length'=>'500',
            'required'=> false,
            'attr' => array('class'=>'form-control form-control-modif'),
        ));

        $builder->add( 'lesson', 'text', array(
            'label' => 'Lesson Title:',
            'max_length'=>'500',
            'required'=>false,
            'attr' => array('class'=>'form-control form-control-modif'),
        ));

        if( $this->params['type'] == 'SingleObject' ) {
            $attr = array('class'=>'form-control form-control-modif');
        } else {
            $attr = array('class' => 'ajax-combobox-optionaluser-educational', 'type' => 'hidden');
        }

        $builder->add('directorstr', 'custom_selector', array(
            'label' => 'Course Director:',
            'attr' => $attr,
            'required'=>false,
            'classtype' => 'optionalUserEducational'
        ));

        if( $this->params['type'] == 'SingleObject' ) {

            $attr = array('class' => 'combobox combobox-width');
            $builder->add('director', 'entity', array(
                'class' => 'OlegOrderformBundle:User',
                'label'=>'Director:',
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
            'data_class' => 'Oleg\OrderformBundle\Entity\Educational'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_educationaltype';
    }
}
