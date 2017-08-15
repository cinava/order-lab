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

namespace Oleg\UserdirectoryBundle\Form;

use Doctrine\ORM\EntityRepository;
use Oleg\UserdirectoryBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HierarchyFilterType extends AbstractType
{

    private $params;


    public function __construct( $params=null )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        $types = array(
//            "default"=>"default",
//            "user-added"=>"user-added",
//            "disabled"=>"disabled",
//            "draft"=>"draft"
//        );
        //sort the choices alphabetically
        $types = array(
            "default"=>"default",
            "disabled"=>"disabled",
            "draft"=>"draft",
            "user-added"=>"user-added",
        );

        $params = array(
            'label'=>'Types:',
            'choices' => $types,
            'required' => false,
            'multiple' => true,
            'attr' => array('class'=>'combobox select2-hierarchy-types') //submit-on-enter-field
        );

        if( $this->params && $this->params['types'] ) {
            $params['data'] = $this->params['types'];
        }

        $builder->add('types','choice',$params);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'filter';
    }
}
