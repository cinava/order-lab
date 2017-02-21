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

class FilterSlideReturnRequestType extends AbstractType
{

    protected $status;

    public function __construct( $status = null )
    {
        $this->status = $status;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $choices = array(   'all' => 'All',
                            'active' => 'Active',
                            'All Scanned & All Returned' => 'All Scanned & All Returned',
                            'Some Scanned & All Returned' => 'Some Scanned & All Returned',
                            'Not Scanned & All Returned' => 'Not Scanned & All Returned',
                            'Checked: Not Received' => 'Checked: Not Received',
                            'Checked: Previously Returned' => 'Checked: Previously Returned',
                            'Checked: Some Returned' => 'Checked: Some Returned',
                            'cancel' => 'Canceled'
                        );

        $builder->add('filter', 'choice',
            array(
                //'mapped' => false,
                'label' => false,
                //'preferred_choices' => array($this->status),
                'attr' => array('class' => 'combobox combobox-width'),
                'choices' => $choices
            )
        );
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        //$resolver->setDefaults(array(
            //'data_class' => 'Oleg\OrderformBundle\Entity\Scan'
        //));
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'filter_search_box';
    }
}
