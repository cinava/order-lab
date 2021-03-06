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

namespace App\UserdirectoryBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserInfoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('suffix', null, array(
            'label' => 'Suffix:',
            'attr' => array('class'=>'form-control')
        ));
        $builder->add('firstName', null, array(
            'label' => 'First Name:',
            'required' => true,
            'attr' => array('class'=>'form-control user-firstName') //'required'=>'required'
        ));
        $builder->add('middleName', null, array(
            'label' => 'Middle Name:',
            'attr' => array('class'=>'form-control')
        ));
        $builder->add('lastName', null, array(
            'label' => 'Last Name:',
            'required' => true,
            'attr' => array('class'=>'form-control user-lastName') //'required'=>'required'
        ));
        $builder->add('email', EmailType::class, array(
            'label' => 'Preferred Email:',
            'required' => true,
            'attr' => array('class'=>'form-control user-email')
        ));
        $builder->add('displayName', null, array(
            'label' => 'Preferred Full Name for Display:',
            'attr' => array('class'=>'form-control')
        ));
        $builder->add('preferredPhone', null, array(
            'label' => 'Preferred Phone Number:',
            'attr' => array('class'=>'form-control phone-mask')
        ));
        $builder->add('initials', null, array(
            'label' => 'Abbreviated name/Initials used by lab staff for deliveries:',
            'attr' => array('class'=>'form-control')
        ));
        $builder->add('salutation', null, array(
            'label' => 'Salutation:',
            'attr' => array('class'=>'form-control')
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\UserdirectoryBundle\Entity\UserInfo',
        ));
    }

    public function getBlockPrefix()
    {
        return 'oleg_userdirectorybundle_userinfo';
    }
}
