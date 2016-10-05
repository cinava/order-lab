<?php

namespace Oleg\CallLogBundle\Form;

use Oleg\CallLogBundle\Form\EncounterDateType;

use Oleg\OrderformBundle\Form\EncounterLocationType;
use Oleg\OrderformBundle\Form\EncounterPatfirstnameType;
use Oleg\OrderformBundle\Form\EncounterPatlastnameType;
use Oleg\OrderformBundle\Form\EncounterPatmiddlenameType;
use Oleg\OrderformBundle\Form\EncounterPatsexType;
use Oleg\OrderformBundle\Form\EncounterPatsuffixType;
use Oleg\OrderformBundle\Form\GenericFieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EncounterType extends AbstractType
{

    protected $params;
    protected $entity;

    public function __construct( $params=null, $entity = null )
    {
        $this->params = $params;
        $this->entity = $entity;

        if( !array_key_exists('alias', $this->params) ) {
            $this->params['alias'] = true;
        }
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('date', 'collection', array(
            'type' => new EncounterDateType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterdate__',
        ));

        $builder->add('patsuffix', 'collection', array(
            'type' => new EncounterPatsuffixType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterpatsuffix__',
        ));
        $builder->add('patlastname', 'collection', array(
            'type' => new EncounterPatlastnameType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterpatlastname__',
        ));
        $builder->add('patfirstname', 'collection', array(
            'type' => new EncounterPatfirstnameType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterpatfirstname__',
        ));
        $builder->add('patmiddlename', 'collection', array(
            'type' => new EncounterPatmiddlenameType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterpatmiddlename__',
        ));

        $builder->add('patsex', 'collection', array(
            'type' => new EncounterPatsexType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterpatsex__',
        ));

//        $attr = array('class'=>'form-control encounterage-field patientage-mask');
//        $gen_attr = array('label'=>"Patient's Age (at the time of encounter):",'class'=>'Oleg\OrderformBundle\Entity\EncounterPatage','type'=>'text');
//        $builder->add('patage', 'collection', array(
//            'type' => new GenericFieldType($this->params, null, $gen_attr, $attr),
//            'allow_add' => true,
//            'allow_delete' => true,
//            'required' => false,
//            'label' => "Patient's Age (at the time of encounter):",
//            'by_reference' => false,
//            'prototype' => true,
//            'prototype_name' => '__encounterpatage__',
//        ));

        //pathistory'
//        $attr = array('class'=>'textarea form-control encounterhistory-field');
//        $gen_attr = array('label'=>"Clinical History (at the time of encounter):",'class'=>'Oleg\OrderformBundle\Entity\EncounterPathistory','type'=>null);
//        $builder->add('pathistory', 'collection', array(
//            'type' => new GenericFieldType($this->params, null, $gen_attr, $attr),
//            'allow_add' => true,
//            'allow_delete' => true,
//            'required' => false,
//            'label' => "Clinical History (at the time of encounter):",
//            'by_reference' => false,
//            'prototype' => true,
//            'prototype_name' => '__encounterpathistory__',
//        ));

        //number and source
        $builder->add('number', 'collection', array(
            'type' => new EncounterNumberType($this->params, $this->entity),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'label' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounternumber__',
        ));

        $builder->add('location', 'collection', array(
            'type' => new EncounterLocationType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterlocation__',
        ));

//            $sources = array('WCMC Epic Ambulatory EMR','Written or oral referral');
//            $params = array('name'=>'Encounter','dataClass'=>'Oleg\OrderformBundle\Entity\EncounterOrder','typename'=>'encounterorder','sources'=>$sources);
//            $builder->add('order', 'collection', array(
//                'type' => new GeneralOrderType($params, null),
//                'allow_add' => true,
//                'allow_delete' => true,
//                'required' => false,
//                'by_reference' => false,
//                'prototype' => true,
//                'prototype_name' => '__encounterorder__',
//            ));
//
//        $builder->add('inpatientinfo', 'collection', array(
//            'type' => new EncounterInpatientinfoType($this->params, null),
//            'allow_add' => true,
//            'allow_delete' => true,
//            'required' => false,
//            'by_reference' => false,
//            'prototype' => true,
//            'prototype_name' => '__encounterinpatientinfo__',
//        ));

        $builder->add('provider', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => 'Provider:',
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :roles OR u=:user')
                        ->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user'] ));
                },
        ));



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
                'prototype_name' => '__encountermessage__',
            ));
        }

        //Referring Provider for calllog new entry
        $builder->add('referringProviders', 'collection', array(
            'type' => new EncounterReferringProviderType($this->params, null),
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__encounterreferringprovider__',
        ));


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Encounter',
            //'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_encountertype';
    }
}
