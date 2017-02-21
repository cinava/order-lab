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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class StateLicenseType extends AbstractType
{

    protected $params;

    public function __construct( $params=null )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add( 'state', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:States',
            //'property' => 'name',
            'label'=>'State:',
            'required'=> false,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('list')
                        ->where("list.type = :typedef OR list.type = :typeadd")
                        ->orderBy("list.orderinlist","ASC")
                        ->setParameters( array(
                            'typedef' => 'default',
                            'typeadd' => 'user-added',
                        ));
                },
        ));

        //country
        $preferredCountries = $this->params['em']->getRepository('OlegUserdirectoryBundle:Countries')->findByName(array('United States'));
        $builder->add( 'country', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:Countries',
            'property' => 'name',
            'label'=>'Country:',
            'required'=> false,
            'multiple' => false,
            'preferred_choices' => $preferredCountries,
            'attr' => array('class'=>'combobox combobox-width'),
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('list')
                        ->where("list.type = :typedef OR list.type = :typeadd")
                        ->orderBy("list.orderinlist","ASC")
                        ->setParameters( array(
                            'typedef' => 'default',
                            'typeadd' => 'user-added',
                        ));
                },
        ));

        $builder->add('licenseNumber', null, array(
            'label' => 'License Number:',
            'attr' => array('class'=>'form-control')
        ));

        $builder->add('licenseIssuedDate', null, array(
            'label' => 'License Issued Date:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
        ));

        $builder->add('licenseExpirationDate', null, array(
            'label' => 'License Expiration Date:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
        ));


//        $builder->add('active', 'checkbox', array(
//            'label' => 'Active:',
//            'attr' => array('class'=>'form-control')
//        ));
        $builder->add('active', null, array(
            'label' => 'Active:',
            'attr' => array('class'=>'combobox')
        ));

        //Medical License(s) section Relevant Documents
        $params = array('labelPrefix'=>'Relevant Document');
        $params['document.showall'] = false;
        $params['document.imageId'] = false;
        $params['document.source'] = false;
        //$params['read_only'] = $readonly;
        $builder->add('attachmentContainer', new AttachmentContainerType($params), array(
            'required' => false,
            'label' => false
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\UserdirectoryBundle\Entity\StateLicense',
        ));
    }

    public function getName()
    {
        return 'oleg_userdirectorybundle_statelicense';
    }
}
