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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CredentialsType extends AbstractType
{

    protected $params;

    public function __construct( $params=null )
    {
        $this->params = $params;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $hasRoleSimpleView = false;
        if( array_key_exists('container', $this->params) ) {
            $hasRoleSimpleView = $this->params['container']->get('security.token_storage')->getToken()->getUser()->hasRole("ROLE_USERDIRECTORY_SIMPLEVIEW");
        }

        $builder->add('dob', 'date', array(
            'label' => 'Date of Birth:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
        ));

        $builder->add('sex', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:SexList',
            'property' => 'name',
            'label' => "Gender:",
            'required'=> false,
            'multiple' => false,
            'attr' => array('class'=>'combobox combobox-width'),
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

        $builder->add('numberCLIA', null, array(
            'label' => 'Clinical Laboratory Improvement Amendments (CLIA) Number:',
            'attr' => array('class'=>'form-control form-control-modif')
        ));

        $builder->add('cliaExpirationDate', 'date', array(
            'label' => 'CLIA Expiration Date:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
        ));

        //Clinical Laboratory Improvement Amendments (CLIA) section Relevant Documents
        $params = array('labelPrefix'=>'Relevant Document');
        $params['document.showall'] = false;
        $params['document.imageId'] = false;
        $params['document.source'] = false;
        //$params['read_only'] = $readonly;
        $builder->add('cliaAttachmentContainer', new AttachmentContainerType($params), array(
            'required' => false,
            'label' => false
        ));

        $builder->add('numberPFI', null, array(
            'label' => 'NY Permanent Facility Identifier (PFI) Number:',
            'attr' => array('class'=>'form-control form-control-modif')
        ));

        $builder->add('numberCOQ', null, array(
            'label' => 'COQ Serial Number:',
            'attr' => array('class'=>'form-control form-control-modif')
        ));

        $builder->add('coqCode', null, array(
            'label' => 'Certificate of Qualification (COQ) Code:',
            'attr' => array('class'=>'form-control form-control-modif')
        ));

        $builder->add('coqExpirationDate', 'date', array(
            'label' => 'COQ Expiration Date:',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control'),
        ));

        //Certificate of Qualification section Relevant Documents
        $params = array('labelPrefix'=>'Relevant Document');
        $params['document.showall'] = false;
        $params['document.imageId'] = false;
        $params['document.source'] = false;
        //$params['read_only'] = $readonly;
        $builder->add('coqAttachmentContainer', new AttachmentContainerType($params), array(
            'required' => false,
            'label' => false
        ));

        $builder->add('emergencyContactInfo', null, array(
            'label' => 'Emergency Contact Information:',
            'attr' => array('class'=>'textarea form-control')
        ));

        if( !$hasRoleSimpleView ) {
            $builder->add('ssn', null, array(
                'label' => 'Social Security Number:',
                'attr' => array('class'=>'form-control form-control-modif')
            ));

            $builder->add('hobby', null, array(
                'label' => 'Hobbies:',
                'attr' => array('class' => 'textarea form-control')
            ));
        }


        $builder->add('codeNYPH', 'collection', array(
            'type' => new CodeNYPHType(),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__codenyph__',
        ));

        $builder->add('stateLicense', 'collection', array(
            'type' => new StateLicenseType($this->params),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__statelicense__',
        ));

        $builder->add('boardCertification', 'collection', array(
            'type' => new BoardCertificationType(),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__boardcertification__',
        ));

        $builder->add('identifiers', 'collection', array(
            'type' => new IdentifierType($this->params),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__identifiers__',
        ));


        $builder->add('citizenships', 'collection', array(
            'type' => new CitizenshipType($this->params),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__citizenships__',
        ));

        $builder->add('examinations', 'collection', array(
            'type' => new ExaminationType($this->params),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => '__examinations__',
        ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\UserdirectoryBundle\Entity\UserPreferences'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oleg_userdirectorybundle_userpreferences';
    }

}
