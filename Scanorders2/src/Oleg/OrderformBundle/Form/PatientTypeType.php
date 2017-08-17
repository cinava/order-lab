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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PatientTypeType extends AbstractType
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


        $builder->add('field', 'entity', array(
            'class' => 'OlegOrderformBundle:PatientTypeList',
            'label' => 'Patient Type',
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('list')
                        //->leftJoin("list.locationTypes", "locationTypes")
                        //->where("locationTypes.name = 'Patient Contact Information'")
                        ->orderBy("list.orderinlist","ASC");
                },
        ));

//        $builder->add( 'sources', null, array(
//            'label' => 'Patient Type Source(s)',
//            'required' => false,
//            'attr' => array('class' => 'combobox combobox-width')
//        ));

        //other fields from abstract
        $builder->add('others', ArrayFieldType::class, array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PatientType',
            'form_custom_value' => $this->params,
            'label' => false,
			'attr' => array('style'=>'display:none;')
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PatientType',
        ));
    }

    public function getBlockPrefix()
    {
        return 'oleg_orderformbundle_patienttypetype';
    }
}
