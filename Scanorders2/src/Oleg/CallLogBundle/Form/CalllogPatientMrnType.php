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

namespace Oleg\CallLogBundle\Form;

use Oleg\OrderformBundle\Form\ArrayFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CalllogPatientMrnType extends AbstractType
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
        echo "mrntype=".$this->params['mrntype']."<br>";
        echo "mrn=".$this->params['mrn']."<br>";

        $builder->add('keytype', 'custom_selector', array(
            'label'=>'MRN Type:',
            'required' => true,
            //'multiple' => false,
            //'data' => 4,
            'data' => $this->params['mrntype'],
            'attr' => array('class' => 'ajax-combobox combobox combobox-width mrntype-combobox mrntype-exception-autogenerated'),
            //'attr' => array('class' => 'ajax-combobox combobox combobox-width mrntype-combobox'),
            'classtype' => 'mrntype'
        ));
//        $builder->add('keytype', 'entity', array(
//            'label'=>'MRN Type:',
//            'attr' => array('class' => 'combobox mrntype-combobox111'),
//            'class' => 'OlegOrderformBundle:MrnType',
//            'query_builder' => function (EntityRepository $er) {
//                return $er->createQueryBuilder('list')
//                    //->select("list.id as id, list.name as text")
//                    ->where("list.type = 'default' AND list.name != 'Auto-generated MRN'")
//                    ->orderBy("list.orderinlist","ASC");
//            },
//        ));

        $builder->add('field', null, array(
            'label' => 'MRN:',
            'required' => false,
            'data' => $this->params['mrn'],
            'attr' => array('class' => 'form-control keyfield patientmrn-mask')
        ));


//        //other fields from abstract
//        $builder->add('others', new ArrayFieldType(), array(
//            'data_class' => 'Oleg\OrderformBundle\Entity\PatientMrn',
//            'label' => false,
//			'attr' => array('style'=>'display:none;')
//        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PatientMrn',
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_mrntype';
    }
}
