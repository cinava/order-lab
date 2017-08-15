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

use Oleg\OrderformBundle\Form\ArrayFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncounterReferringProviderType extends AbstractType
{

    protected $params;
    protected $entity;

    public function __construct( $params=null, $entity = null )
    {
        $this->params = $params;
        $this->entity = $entity;


        if( !array_key_exists('referringProviders-readonly', $this->params) ) {
            $this->params['referringProviders-readonly'] = true;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        $builder->add('field', 'date', array(
//            'label' => "Encounter Date:",
//            'widget' => 'single_text',
//            'required' => false,
//            'format' => 'MM/dd/yyyy',   //used for birth day only (no hours), so we don't need to set view_timezone
//            'attr' => array('class' => 'datepicker form-control encounter-date', 'style'=>'margin-top: 0;'),
//        ));

//        $builder->add('others', new ArrayFieldType($this->params), array(
//            'data_class' => 'Oleg\OrderformBundle\Entity\EncounterReferringProvider',
//            'label' => false,
//			'attr' => array('style'=>'display:none;')
//        ));

//        //extra data-structure fields
//        //echo "datastructure time <br>";
//        $builder->add('time', 'time', array(
//            'input'  => 'datetime',
//            'widget' => 'choice',
//            'label'=>'Encounter Time:'
//        ));


        $builder->add('field', 'custom_selector', array(
            'label' => 'Referring Provider Name:',
            'attr' => array('class' => 'combobox combobox-width ajax-combobox-encounterReferringProvider'),
            'read_only' => $this->params['referringProviders-readonly'],
            'required' => false,
            'classtype' => 'singleUserWrapper'
            //'classtype' => 'userWrapper'
        ));

        $builder->add('referringProviderSpecialty', 'custom_selector', array(
            'label' => 'Referring Provider Specialty:',
            'attr' => array('class' => 'combobox combobox-width ajax-combobox-referringProviderSpecialty'),
            'read_only' => $this->params['referringProviders-readonly'],
            'required' => false,
            'classtype' => 'referringProviderSpecialty'
        ));

        $builder->add('referringProviderPhone', null, array(
            'label' => 'Referring Provider Phone Number:',
            'read_only' => $this->params['referringProviders-readonly'],
            'attr' => array('class'=>'form-control')
        ));

        $builder->add('referringProviderEmail', null, array(
            'label' => 'Referring Provider E-Mail:',
            'attr' => array('class'=>'form-control'),
            'read_only' => $this->params['referringProviders-readonly'],
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\EncounterReferringProvider',
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_encounterreferringprovidertype';
    }
}
