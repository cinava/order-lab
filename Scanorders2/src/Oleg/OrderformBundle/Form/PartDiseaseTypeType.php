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

use Oleg\OrderformBundle\Form\CustomType\ScanCustomSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Oleg\OrderformBundle\Helper\FormHelper;

class PartDiseaseTypeType extends AbstractType
{

    protected $params;

    public function formConstructor( $params=null )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value']);

        //New in Symfony 2.8: choices is array
        //get array of diseaseTypes
        $repository = $this->params['em']->getRepository('OlegOrderformBundle:DiseaseTypeList');
        $dql = $repository->createQueryBuilder("list")->orderBy("list.orderinlist","ASC");
        $query = $this->params['em']->createQuery($dql);
        $items = $query->getResult();
        $diseaseTypesArr = array();
        foreach( $items as $item ) {
            $diseaseTypesArr[] = $item;
        }
        //echo "count items=".count($diseaseTypesArr)."<br>";
        //exit();

        $builder->add( 'diseaseTypes', 'entity', array(
            'class' => 'OlegOrderformBundle:DiseaseTypeList',
            'label'=>'Type of Disease:',
            'required'=>false,
            'multiple' => true,
            'expanded' => true,
            'attr' => array('class' => 'horizontal_type diseaseType'), //'required' => '0', 'disabled'
            'choices' => $diseaseTypesArr
//            'choices' => function(EntityRepository $er) {
//                    //return $er->createQueryBuilder('list')
//                    //    ->orderBy("list.orderinlist","ASC");
//                    $query = $er->createQueryBuilder('list')
//                        ->orderBy("list.orderinlist","ASC");
//                    $items = $query->getResult();
//                    $itemsArr = array();
//                    foreach( $items as $item ) {
//                        $itemsArr[] = $item;
//                    }
//                    echo "count items=".count($itemsArr)."<br>";
//                    exit();
//                    return $itemsArr;
//                },
        ));

        //get array of diseaseTypes
        $repository = $this->params['em']->getRepository('OlegOrderformBundle:DiseaseOriginList');
        $dql = $repository->createQueryBuilder("list")->orderBy("list.orderinlist","ASC");
        $query = $this->params['em']->createQuery($dql);
        $items = $query->getResult();
        $DiseaseOriginListArr = array();
        foreach( $items as $item ) {
            $DiseaseOriginListArr[] = $item;
        }

        $builder->add( 'diseaseOrigins', 'entity', array(
            'class' => 'OlegOrderformBundle:DiseaseOriginList',
            'label'=>'Origin:',
            'required'=>false,
            'multiple' => true,
            'expanded' => true,
            'attr' => array('class' => 'horizontal_type origin-checkboxes'), //'required' => '0', 'disabled'
            'choices' => $DiseaseOriginListArr
//            'choices' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('list')
//                        ->orderBy("list.orderinlist","ASC");
//                },
        ));

        $builder->add('primaryOrgan', ScanCustomSelectorType::class, array(
            'label' => 'Primary Site of Origin:',
            'attr' => array('class' => 'ajax-combobox ajax-combobox-organ', 'type' => 'hidden'),
            'required' => false,
            'classtype' => 'sourceOrgan'
        ));

        $builder->add('others', ArrayFieldType::class, array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PartDiseaseType',
            'form_custom_value' => $this->params,
            'label' => false,
			'attr' => array('style'=>'display:none;')
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PartDiseaseType',
            'form_custom_value' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'oleg_orderformbundle_partdiseasetypetype';
    }
}
