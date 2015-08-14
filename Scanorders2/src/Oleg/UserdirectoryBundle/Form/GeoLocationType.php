<?php

namespace Oleg\UserdirectoryBundle\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class GeoLocationType extends AbstractType
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


        $builder->add('street1',null,array(
            'label'=>'Street Address [Line 1]:',
            'attr' => array('class'=>'form-control geo-field-street1')
        ));

        $builder->add('street2',null,array(
            'label'=>'Street Address [Line 2]:',
            'attr' => array('class'=>'form-control geo-field-street2')
        ));

        $builder->add('city', 'employees_custom_selector', array(
            'label' => 'City:',
            'required' => false,
            'attr' => array('class' => 'ajax-combobox-city', 'type' => 'hidden'),
            'classtype' => 'city'
        ));

        //state
        $stateArray = array(
            'class' => 'OlegUserdirectoryBundle:States',
            //'property' => 'name',
            'label'=>'State:',
            'required'=> false,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width geo-field-state'),
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('list')
                        ->where("list.type = :typedef OR list.type = :typeadd")
                        ->orderBy("list.orderinlist","ASC")
                        ->setParameters( array(
                            'typedef' => 'default',
                            'typeadd' => 'user-added',
                        ));
                },
        );
        if( $this->params['cycle'] == 'new_standalone' ) {
            $stateArray['data'] = $this->params['em']->getRepository('OlegUserdirectoryBundle:States')->findOneByName('New York');
        }
        $builder->add( 'state', 'entity', $stateArray);

        //country
        $countryArray = array(
            'class' => 'OlegUserdirectoryBundle:Countries',
            'property' => 'name',
            'label'=>'Country:',
            'required'=> false,
            'multiple' => false,
            //'preferred_choices' => $preferredCountries,
            'attr' => array('class'=>'combobox combobox-width geo-field-country'),
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('list')
                        ->where("list.type = :typedef OR list.type = :typeadd")
                        ->orderBy("list.orderinlist","ASC")
                        ->setParameters( array(
                            'typedef' => 'default',
                            'typeadd' => 'user-added',
                        ));
                },
        );
        $countryArray['preferred_choices'] = $this->params['em']->getRepository('OlegUserdirectoryBundle:Countries')->findByName(array('United States'));
        if( $this->params['cycle'] == 'new_standalone' ) {
            $countryArray['data'] = $this->params['em']->getRepository('OlegUserdirectoryBundle:Countries')->findOneByName('United States');
        }
        $builder->add( 'country', 'entity', $countryArray);

        $builder->add('county',null,array(
            'label'=>'County:',
            'attr' => array('class'=>'form-control geo-field-county')
        ));

        $builder->add('zip',null,array(
            'label'=>'Zip Code:',
            'attr' => array('class'=>'form-control geo-field-zip')
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\UserdirectoryBundle\Entity\GeoLocation',
        ));
    }

    public function getName()
    {
        return 'oleg_userdirectorybundle_geolocation';
    }
}
