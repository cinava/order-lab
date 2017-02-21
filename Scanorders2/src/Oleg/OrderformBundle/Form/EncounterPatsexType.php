<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EncounterPatsexType extends AbstractType
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

//        $builder->add( 'field', 'choice', array(
//            'label'=>"Patient's Sex (at the time of encounter)",
//            'choices' => array("Female"=>"Female", "Male"=>"Male", "Unspecified"=>"Unspecified"),
//            'multiple' => false,
//            'expanded' => true,
//            'attr' => array('class' => 'horizontal_type encountersex-field')
//        ));

//        $builder->add( 'field', null, array(
//            'label' => "Patient's Sex (at the time of encounter)",
//            'required' => false,
//            'attr' => array('class' => 'combobox combobox-width encountersex-field')
//        ));

        $builder->add( 'field', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:SexList',
            'property' => 'name',
            'label' => "Patient's Gender (at the time of encounter):",
            'required'=> false,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width encountersex-field'),
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('list')
                        ->where("list.type = :typedef OR list.type = :typeadd")
                        ->orderBy("list.orderinlist","ASC")
                        ->setParameters( array(
                            'typedef' => 'default',
                            'typeadd' => 'user-added'
                        ));
                },
        ));

        $builder->add('others', new ArrayFieldType($this->params), array(
            'data_class' => 'Oleg\OrderformBundle\Entity\EncounterPatsex',
            'label' => false,
			'attr' => array('style'=>'display:none;')
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\EncounterPatsex',
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_encounterpatsex';
    }
}
