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

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class BaseTitleType extends AbstractType
{

    protected $params;

    public function __construct( $params )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $hasRoleSimpleView = false;
        if( array_key_exists('sc', $this->params) ) {
            $hasRoleSimpleView = $this->params['sc']->getToken()->getUser()->hasRole("ROLE_USERDIRECTORY_SIMPLEVIEW");
        }

        $builder->add('id','hidden',array('label'=>false));

//        $builder->add( 'name', 'text', array(
//            'label'=>$this->params['label'].' Title:',   //'Admnistrative Title:',
//            'required'=>false,
//            'attr' => array('class' => 'form-control')
//        ));
        $builder->add('name', 'employees_custom_selector', array(
            'label'=>$this->params['label'].' Title:',
            'attr' => array('class' => 'ajax-combobox-'.$this->params['formname'], 'type' => 'hidden'),
            'required' => false,
            'classtype' => $this->params['formname']
        ));

        $builder->add('startDate', 'date', array(
            'label' => $this->params['label']." Title Start Date:",
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control allow-future-date'),
        ));

        $builder->add('endDate', 'date', array(
            'label' => $this->params['label']." Title End Date:",
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control allow-future-date user-expired-end-date'),
        ));

        $baseUserAttr = new $this->params['fullClassName']();
        $builder->add('status', 'choice', array(
            'disabled' => ($this->params['read_only'] ? true : false),
            'choices'   => array(
                $baseUserAttr::STATUS_UNVERIFIED => $baseUserAttr->getStatusStrByStatus($baseUserAttr::STATUS_UNVERIFIED),
                $baseUserAttr::STATUS_VERIFIED => $baseUserAttr->getStatusStrByStatus($baseUserAttr::STATUS_VERIFIED)
            ),
            'label' => "Status:",
            'required' => true,
            'attr' => array('class' => 'combobox combobox-width'),
        ));

        //priority
        if( !$hasRoleSimpleView ) {
            $builder->add('priority', 'choice', array(
                'choices' => array(
                    '0' => 'Primary',
                    '1' => 'Secondary'
                ),
                'label' => $this->params['label'] . " Title Type:",
                'required' => false,
                'attr' => array('class' => 'combobox combobox-width'),
            ));
        }

        ///////////////////////// tree node /////////////////////////
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $title = $event->getData();
            $form = $event->getForm();

            $institution = null;
            $label = null;
            if( $title ) {
                $institution = $title->getInstitution();
                if( $institution ) {
                    $label = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->getLevelLabels($institution) . ":";
                }
            }
            if( !$label ) {
                $label = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->getLevelLabels(null) . ":";
            }
            //echo "label=".$label."<br>";

            $treeFieldArray = array(
                'label' => $label,
                'required' => false,
                'classtype' => 'institution'
            );

            $attrArray = array(
                'class' => 'ajax-combobox-compositetree',
                'type' => 'hidden',
                'data-compositetree-bundlename' => 'UserdirectoryBundle',
                'data-compositetree-classname' => 'Institution',
                //'data-compositetree-params' => $treeParams
            );

            /////////////// preset default institution ////////////////
            if( !$institution ) {
                //$treeParams = null;
                $treeData = null;
                $newInstitution = null;
                $mapper = array(
                    'prefix' => "Oleg",
                    'className' => "Institution",
                    'bundleName' => "UserdirectoryBundle"
                );
                //preset default institution for AdministrativeTitle - Weill Cornell or New York Presbyterian Hospital
                if ($this->params['fullClassName'] == "Oleg\UserdirectoryBundle\Entity\AdministrativeTitle") {
                    //echo "AdministrativeTitle<br>"; //$treeParams = "entityIds=1,106";
                    $wcmc = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("WCMC");
                    if( $wcmc ) {
                        $newInstitution = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent(
                            "Pathology and Laboratory Medicine",
                            $wcmc,
                            $mapper
                        );
                    }
                }
                //preset default institution for AppointmentTitle (Academic Title) - Weill Cornell
                if ($this->params['fullClassName'] == "Oleg\UserdirectoryBundle\Entity\AppointmentTitle") {
                    //echo "AppointmentTitle<br>"; //$treeParams = "entityIds=1";
                    $wcmc = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("WCMC");
                    if( $wcmc ) {
                        $newInstitution = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent(
                            "Pathology and Laboratory Medicine",
                            $wcmc,
                            $mapper
                        );
                    }
                }
                //preset default institution for MedicalTitle (Academic Title) - New York Presbyterian Hospital
                if ($this->params['fullClassName'] == "Oleg\UserdirectoryBundle\Entity\MedicalTitle") {
                    //echo "MedicalTitle<br>"; //$treeParams = "entityIds=106";
                    $nyp = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("NYP");
                    if ($nyp) {
                        $newInstitution = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent(
                            "Pathology and Laboratory Medicine",
                            $nyp,
                            $mapper
                        );
                    }
                }

                if( $newInstitution ) {
                    $treeFieldArray['data'] = $newInstitution->getId();
                    $treeFieldArray['label'] = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->getLevelLabels($newInstitution) . ":";
                }
                //if( $treeParams ) {
                    //$attrArray['data-compositetree-params'] = $treeParams;
                //}
            }
            /////////////// EOF preset default institution ////////////////

            $treeFieldArray['attr'] = $attrArray;

            $form->add('institution', 'employees_custom_selector', $treeFieldArray);
        });
        ///////////////////////// EOF tree node /////////////////////////

        if( !$hasRoleSimpleView ) {
            $builder->add('effort', 'employees_custom_selector', array(
                'label' => 'Percent Effort:',
                'attr' => array('class' => 'ajax-combobox-effort', 'type' => 'hidden', "data-inputmask" => "'mask': '[o]', 'repeat': 10, 'greedy' : false"),
                'required' => false,
                'classtype' => 'effort'
            ));
        }


        if( $this->params['cycle'] != "show" ) {
            $builder->add('orderinlist',null,array(
                'label'=>'Display Order:',
                'required' => false,
                'attr' => array('class'=>'form-control')
            ));
        }

        //position, residencyTrack, fellowshipType, pgy for AppointmentTitle (Academic Appointment Title)
        if( $this->params['fullClassName'] == "Oleg\UserdirectoryBundle\Entity\AppointmentTitle" ) {
//            $builder->add('position', 'choice', array(
//                'choices'   => array(
//                    'Resident'   => 'Resident',
//                    'Fellow' => 'Fellow',
//                    'Clinical Faculty' => 'Clinical Faculty',
//                    'Research Faculty' => 'Research Faculty'
//                    //'Clinical Faculty, Research Faculty' => 'Clinical Faculty, Research Faculty'
//                ),
//                'label' => "Position Track Type:",
//                'required' => false,
//                'attr' => array('class' => 'combobox combobox-width appointmenttitle-position-field', 'onchange'=>'positionTypeAction(this)'),
//            ));
            $builder->add( 'positions', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:PositionTrackTypeList',
                'property' => 'name',
                'label'=>'Position Track Type(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width appointmenttitle-position-field', 'onchange'=>'positionTypeAction(this)'),
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

            $builder->add( 'residencyTrack', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:ResidencyTrackList',
                'property' => 'name',
                'label'=>'Residency Track:',
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

            $builder->add('fellowshipType', 'employees_custom_selector', array(
                'label' => "Fellowship Type:",
                'required' => false,
                'attr' => array('class' => 'combobox combobox-width ajax-combobox-fellowshiptype', 'type' => 'hidden'),
                'classtype' => 'fellowshiptype'
            ));

            $builder->add('pgystart', 'date', array(
                'label' => "During academic year that started on:",
                'widget' => 'single_text',
                'required' => false,
                'format' => 'MM/dd/yyyy',
                'attr' => array('class' => 'datepicker form-control pgystart-field', 'style'=>'margin-top: 0;'),
            ));

            $builder->add('pgylevel',null,array(
                'label'=>'The Post Graduate Year (PGY) level was:',
                'required' => false,
                'attr' => array('class'=>'form-control pgylevel-field')
            ));

            $builder->add('pgylevelexpected','integer',array(
                'label' => 'Expected Current Post Graduate Year (PGY) level:',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
                'attr' => array('class'=>'form-control pgylevelexpected-field')
            ));

        }


        //boss
        if( $this->params['fullClassName'] == "Oleg\UserdirectoryBundle\Entity\AdministrativeTitle" ) {

//            $builder->add('boss','entity',array(
//                'class' => 'OlegUserdirectoryBundle:User',
//                'label' => "Reports to:",
//                'multiple' => true,
//                'attr' => array('class'=>'combobox combobox-width'),
//                'required' => false
//            ));
            $builder->add( 'boss', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:User',
                'label'=>'Reports to:',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('list')
                            ->leftJoin("list.infos", "infos")
                            ->leftJoin("list.employmentStatus", "employmentStatus")
                            ->leftJoin("employmentStatus.employmentType", "employmentType")
                            ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                            ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                            ->orderBy("infos.displayName","ASC");
                    },
            ));

            $builder->add('userPositions','entity',array(
                'class' => 'OlegUserdirectoryBundle:PositionTypeList',
                'label' => "Position Type:",
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'required' => false
            ));

//            $builder->add( 'supervisorInstitution', null, array(
//                'label'=>'Head of this institution:',
//                'required'=>false,
//                'attr' => array('class'=>'form-control', 'style'=>'margin:0')
//            ));
//
//            $builder->add( 'supervisorDepartment', null, array(
//                'label'=>'Head of this department:',
//                'required'=>false,
//                'attr' => array('class'=>'form-control', 'style'=>'margin:0')
//            ));
//
//            $builder->add( 'supervisorDivision', null, array(
//                'label'=>'Head of this division:',
//                'required'=>false,
//                'attr' => array('class'=>'form-control', 'style'=>'margin:0')
//            ));
//
//            $builder->add( 'supervisorService', null, array(
//                'label'=>'Head of this service:',
//                'required'=>false,
//                'attr' => array('class'=>'form-control', 'style'=>'margin:0')
//            ));

        }


        //specialties for Medical Appointment Title)
        if( $this->params['fullClassName'] == "Oleg\UserdirectoryBundle\Entity\MedicalTitle" ) {

            $builder->add( 'specialties', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:MedicalSpecialties',
                'property' => 'name',
                'label'=>'Specialty(s):',
                'required'=> false,
                'multiple' => true,
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

            $builder->add('userPositions','entity',array(
                'class' => 'OlegUserdirectoryBundle:PositionTypeList',
                'label' => "Position Type:",
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'required' => false
            ));

        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->params['fullClassName'],
            //'csrf_protection' => false,
            'allow_extra_fields' => true
        ));
    }

    public function getName()
    {
        return 'oleg_userdirectorybundle_'.$this->params['formname'];
    }
}
