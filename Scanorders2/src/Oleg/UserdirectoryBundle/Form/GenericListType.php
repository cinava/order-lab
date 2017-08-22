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
use Oleg\UserdirectoryBundle\Entity\CompositeNodeInterface;
use Oleg\UserdirectoryBundle\Entity\FormNode;
use Oleg\UserdirectoryBundle\Entity\Institution;
use Oleg\UserdirectoryBundle\Entity\PlatformListManagerRootList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenericListType extends AbstractType
{

    protected $params;
    protected $mapper;


    public function formConstructor( $params, $mapper )
    {
        $this->params = $params;
        $this->mapper = $mapper;

        if( !array_key_exists('parentClassName', $this->mapper) ) {
            $this->mapper['parentClassName'] = $this->mapper['className'];
        }

    }

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value'],$options['form_custom_value_mapper']);

        //ListType($this->params, $this->mapper)
        $builder->add('list', ListType::class, array(
            'form_custom_value' => $this->params,
            'form_custom_value_mapper' => $this->mapper,
            'data_class' => $this->mapper['fullClassName'],
            'label' => false
        ));


        //tree classes: BaseCompositeNode (Institution, MessageCatergory, CommentTypeList), FellowshipSubspecialty
        if( method_exists($this->params['entity'],'getParent') ) {
            //echo "cycle=".$this->params['cycle']."<br>";
            if( $this->params['cycle'] == "show" ) {
                $attr = array('class' => 'combobox combobox-width', 'readonly'=>'readonly');
            } else {
                $attr = array('class' => 'combobox combobox-width');
            }
            $builder->add('parent',null,array(
                'label' => $this->mapper['parentClassName'].' (Parent):',
                'choice_label' => 'getTreeName',
                //'attr' => array('class' => 'combobox combobox-width')
                'attr' => $attr
            ));

        }


        //TODO: make it as institutional tree?
        if( method_exists($this->params['entity'],'getInstitution') ) {

            $this->where = "list.type = :typedef OR list.type = :typeadd";

            //FellowshipSubspecialty
            if( strtolower($this->mapper['className']) == strtolower("FellowshipSubspecialty") ) {
                $this->where = "(list.type = :typedef OR list.type = :typeadd) AND list.level=1";
            }

            //echo "show institution<br>";

//            $builder->add('institution',EntityType::class,array(
//                'class' => 'OlegUserdirectoryBundle:Institution',
//                'label' => "Institution:",
//                'choice_label' => "getTreeName",
//                'multiple' => false,
//                'attr' => array('class'=>'combobox combobox-width'),
//                'required' => false,
//            ));

            $builder->add( 'institution', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:Institution',
                'choice_label' => 'getTreeName',
                'label'=>'Institution:',
                'required'=> false,
                'multiple' => false,
                'attr' => array('class'=>'combobox combobox-width'),
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('list')
                            ->leftJoin("list.children","children")
                            //->where("(list.type = :typedef OR list.type = :typeadd) AND list.level=1")
                            ->where($this->where)
                            ->orderBy("list.orderinlist","ASC")
                            ->setParameters( array(
                                'typedef' => 'default',
                                'typeadd' => 'user-added',
                            ));
                    },
            ));

//            ///////////////////////// tree node /////////////////////////
//            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//                $title = $event->getData();
//                $form = $event->getForm();
//
//                echo "2 show institution<br>";
//
//                $label = null;
//                if( $title ) {
//                    $institution = $title->getInstitution();
//                    if( $institution ) {
//                        $label = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->getLevelLabels($institution) . ":";
//                    }
//                }
//                if( !$label ) {
//                    $label = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->getLevelLabels(null) . ":";
//                }
//                echo "label=".$label."<br>";
//
//                $form->add('institution', 'employees_custom_selector', array(
//                    'label' => $label,
//                    'required' => false,
//                    //'attr' => array('class' => 'ajax-combobox-institution', 'type' => 'hidden'),
//                    'attr' => array(
//                        'class' => 'ajax-combobox-compositetree',
//                        'type' => 'hidden',
//                        'data-compositetree-bundlename' => 'UserdirectoryBundle',
//                        'data-compositetree-classname' => 'Institution'
//                    ),
//                    'classtype' => 'institution'
//                ));
//            });
//            ///////////////////////// EOF tree node /////////////////////////

        } //getInstitution

        if( method_exists($this->params['entity'],'getRoot') ) {
            $builder->add( 'root', null, array(
                'label'=>'Root:',
                //'disabled' => true,
                'attr' => array('class'=>'form-control', 'readonly'=>true),
            ));
        }
        if( method_exists($this->params['entity'],'getLft') ) {
            $builder->add( 'lft', null, array(
                'label'=>'Left:',
                //'disabled' => true,
                'attr' => array('class'=>'form-control', 'readonly'=>true),
            ));
        }
        if( method_exists($this->params['entity'],'getRgt') ) {
            $builder->add( 'rgt', null, array(
                'label'=>'Right:',
                //'disabled' => true,
                'attr' => array('class'=>'form-control', 'readonly'=>true),
            ));
        }


        if( method_exists($this->params['entity'],'getInstitutions') ) {
            //echo "add institutions <br>";
            $builder->add( 'institutions', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:Institution',
                'choice_label' => 'getTreeName',
                'label'=>'Institutions:',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('list')
                            ->leftJoin("list.children","children")
                            ->where("list.type = :typedef OR list.type = :typeadd")
                            ->orderBy("list.orderinlist","ASC")
                            ->setParameters( array(
                                'typedef' => 'default',
                                'typeadd' => 'user-added',
                            ));
                    },
            ));
        }

        //Show Collaborations in the Institution object
        if( method_exists($this->params['entity'],'getCollaborations') ) {


            ///////////////////////// tree node /////////////////////////
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $title = $event->getData();
                $form = $event->getForm();

                //check if this Institution is under "All Collaborations" tree
                $allCollaborationInst = $this->params['em']->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("All Collaborations");
                if( $title->getRoot() != $allCollaborationInst->getRoot() ) {
                    return;
                }

                //echo "show Collaboration institutions<br>";

                $form->add( 'collaborationInstitutions', EntityType::class, array(
                    'class' => 'OlegUserdirectoryBundle:Institution',
                    'choice_label' => 'getTreeName',
                    'label'=>'Institutions:',
                    'required'=> false,
                    'multiple' => true,
                    'attr' => array('class'=>'combobox combobox-width'),
                    'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('list')
                                ->leftJoin("list.children","children")
                                ->where("list.type = :typedef OR list.type = :typeadd")
                                ->orderBy("list.orderinlist","ASC")
                                ->setParameters( array(
                                    'typedef' => 'default',
                                    'typeadd' => 'user-added',
                                ));
                        },
                ));

                $form->add( 'collaborationType', EntityType::class, array(
                    'class' => 'OlegUserdirectoryBundle:CollaborationTypeList',
                    'choice_label' => 'name',
                    'label'=>'Collaboration Type:',
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

            });
            ///////////////////////// EOF tree node /////////////////////////

//            $builder->add( 'collaborations', EntityType::class, array(
//                'class' => 'OlegUserdirectoryBundle:Collaboration',
//                //'disabled' => true,
//                //'choice_label' => 'getTreeName',
//                'label'=>'Collaborations:',
//                'required'=> false,
//                'multiple' => true,
//                'attr' => array('class'=>'combobox combobox-width'),
//                'query_builder' => function(EntityRepository $er) {
//                        return $er->createQueryBuilder('list')
//                            //->leftJoin("list.children","children")
//                            ->where("list.type = :typedef OR list.type = :typeadd")
//                            ->orderBy("list.orderinlist","ASC")
//                            ->setParameters( array(
//                                'typedef' => 'default',
//                                'typeadd' => 'user-added',
//                            ));
//                    },
//            ));
        }

        //Collaboration
//        if( method_exists($this->params['entity'],'getCollaborationType') ) {
//            //echo "add institutions <br>";
//            $builder->add( 'collaborationType', EntityType::class, array(
//                'class' => 'OlegUserdirectoryBundle:CollaborationTypeList',
//                'choice_label' => 'name',
//                'label'=>'Collaboration Type:',
//                'required'=> false,
//                'multiple' => false,
//                'attr' => array('class'=>'combobox combobox-width'),
//                'query_builder' => function(EntityRepository $er) {
//                        return $er->createQueryBuilder('list')
//                            ->where("list.type = :typedef OR list.type = :typeadd")
//                            ->orderBy("list.orderinlist","ASC")
//                            ->setParameters( array(
//                                'typedef' => 'default',
//                                'typeadd' => 'user-added',
//                            ));
//                    },
//            ));
//        }

        //tree: add group title
        if( method_exists($this->params['entity'],'getOrganizationalGroupType') ) {
            $builder->add('organizationalGroupType',null,array(
                'label' => 'Organizational Group Type:',
                'attr' => array('class' => 'combobox combobox-width')
            ));
        }

        //types
        if( method_exists($this->params['entity'],'getTypes') ) {
            //echo "cycle=".$this->params['cycle']."<br>";
            if( $this->params['cycle'] == "show" ) {
                $attr = array('class' => 'combobox combobox-width', 'readonly'=>'readonly');
            } else {
                $attr = array('class' => 'combobox combobox-width');
            }
            $builder->add('types',null,array(
                'label' => $this->mapper['className'].' Type(s):',
                'attr' => $attr
            ));
        }

        //url
        if( method_exists($this->params['entity'],'getUrl') ) {
            $builder->add('url',null,array(
                'label' => 'Url:',
                'attr' => array('class' => 'form-control')
            ));
        }
        if( method_exists($this->params['entity'],'getExclusivelySites') ) {
            $builder->add('exclusivelySites',EntityType::class,array(
                'class' => 'OlegUserdirectoryBundle:SiteList',
                'label' => 'Apply Url exclusively to Site(s):',
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'required' => false,
            ));
        }

        //PermissionList
        if( strtolower($this->mapper['className']) == strtolower("PermissionList") ) {
            $builder->add('permissionObjectList',EntityType::class,array(
                'class' => 'OlegUserdirectoryBundle:PermissionObjectList',
                'label' => "Object:",
                'multiple' => false,
                'required' => false,
                'attr' => array('class'=>'combobox'),
            ));
            $builder->add('permissionActionList',EntityType::class,array(
                'class' => 'OlegUserdirectoryBundle:PermissionActionList',
                'label' => "Action:",
                'multiple' => false,
                'required' => false,
                'attr' => array('class'=>'combobox'),
            ));
        }

        //Roles
        if( strtolower($this->mapper['className']) == strtolower("Roles") ) {

            $builder->add('alias',null,array(
                'label'=>'Alias:',
                'attr' => array('class' => 'form-control')
            ));
            $builder->add('attributes',EntityType::class,array(
                'class' => 'OlegUserdirectoryBundle:RoleAttributeList',
                'label' => "Attribute(s):",
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'required' => false,
            ));

            //permissions: show list of
            $builder->add('permissions', CollectionType::class, array(
                'entry_type' => PermissionType::class,
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__permissions__',
            ));

//            $builder->add('sites',EntityType::class,array(
//                'class' => 'OlegUserdirectoryBundle:SiteList',
//                'label' => "Site(s):",
//                'multiple' => true,
//                'attr' => array('class'=>'combobox combobox-width'),
//                'required' => false,
//            ));

            $builder->add('level',null,array(
                'label' => "Level:",
                'attr' => array('class'=>'form-control'),
                'required' => false,
            ));

            $builder->add('fellowshipSubspecialty',EntityType::class,array(
                'class' => 'OlegUserdirectoryBundle:FellowshipSubspecialty',
                'label' => "FellowshipSubspecialty:",
                'multiple' => false,
                'required' => false,
                'attr' => array('class'=>'combobox combobox-width')
            ));
        }

        if( method_exists($this->params['entity'],'getSites') ) {
            $builder->add('sites',EntityType::class,array(
                'class' => 'OlegUserdirectoryBundle:SiteList',
                'label' => "Site(s):",
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'required' => false,
            ));
        }

        //Role Attributes
        if( strtolower($this->mapper['className']) == strtolower("RoleAttributeList") || strtolower($this->mapper['className']) == strtolower("FellAppRank") ) {
            $builder->add('value',null,array(
                'label'=>'Value:',
                'attr' => array('class' => 'form-control')
            ));
        }

        //nativeName for Language List
        if( strtolower($this->mapper['className']) == strtolower("LanguageList") ) {
            $builder->add('nativeName',null,array(
                'label'=>'Native Name:',
                'attr' => array('class' => 'form-control')
            ));
        }

        //level for OrganizationalGroupType
        if(
            strtolower($this->mapper['className']) == strtolower("OrganizationalGroupType") ||
            strtolower($this->mapper['className']) == strtolower("MessageTypeClassifiers") ||
            strtolower($this->mapper['className']) == strtolower("CommentGroupType") ||
            strtolower($this->mapper['className']) == strtolower("ResearchGroupType") ||
            strtolower($this->mapper['className']) == strtolower("CourseGroupType") ||
            strtolower($this->mapper['className']) == strtolower("PatientListHierarchyGroupType")
        ) {
//        if( method_exists($this->params['entity'],'getLevel') ) {
            $builder->add('level',null,array(
                'label'=>'Default Tree Level Association:',
                'attr' => array('class' => 'form-control')
            ));
        }

        //fields for Tree implements CompositeNodeInterface
        if( $this->params['entity'] instanceof CompositeNodeInterface ) {
            //always read only - do not allow to change level
            $builder->add('level',null,array(
                'label'=>'Level:',
                //'disabled' => true,
                'attr' => array('class' => 'form-control')
            ));
            //always read only - do not allow to change parent
            $builder->add('parent',null,array(
                'label' => $this->mapper['parentClassName'].' (Parent):',
                'choice_label' => 'getTreeName',
                'attr' => array('class' => 'combobox combobox-width', 'readonly'=>'readonly')
            ));
        }

        //PlatformListManagerRootList
        if( $this->params['entity'] instanceof PlatformListManagerRootList ) {
            $builder->add('listName',null,array(
                'label'=>'Database Entity Name:',
                'attr' => array('class'=>'form-control')
            ));
            $builder->add('listRootName',null,array(
                'label'=>'Root Name:',
                'attr' => array('class'=>'form-control')
            ));
        }

        //FormNode Holder
        if( method_exists($this->params['entity'],'getFormNodes') ) {
            $builder->add( 'formNodes', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:FormNode',
                'choice_label' => 'getTreeNameObjectType',
                'label'=>'Form Node(s):',
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
        }

        //FormNode
        if( $this->params['entity'] instanceof FormNode ) {
            $builder->add('showLabel', CheckboxType::class, array(
                'label' => 'Show Field Label:',
                'required' => false,
                'attr' => array('class'=>'form-control', 'style'=>'margin:0')
            ));
            $builder->add('placeholder', TextType::class, array(
                'label' => 'Placeholder:',
                'required' => false,
                'attr' => array('class'=>'form-control')
            ));
            $builder->add('visible', CheckboxType::class, array(
                'label' => 'Visible:',
                'required' => false,
                'attr' => array('class'=>'form-control', 'style'=>'margin:0')
            ));
        }

        //ReceivedValueEntity
        if( method_exists($this->params['entity'],'getReceivedValueEntityName') ) {
            $builder->add('receivedValueEntityNamespace',null,array(
                'label' => "Received Value Entity Namespace:",
                'required' => false,
                'attr' => array('class'=>'form-control'),
            ));
            $builder->add('receivedValueEntityName',null,array(
                'label' => "Received Value Entity Name:",
                'required' => false,
                'attr' => array('class'=>'form-control'),
            ));
            $builder->add('receivedValueEntityId',null,array(
                'label' => "Received Value Entity Id:",
                'required' => false,
                'attr' => array('class'=>'form-control'),
            ));
        }

        if( strtolower($this->mapper['className']) == strtolower("UserWrapper") ) {
            $builder->add( 'user', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:User',
                'label'=>'Linked User:',
                'required'=> false,
                'multiple' => false,
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

//            $builder->add('referringProviderSpecialty', 'custom_selector', array(
//                'label' => 'Referring Provider Specialty:',
//                'attr' => array('class' => 'combobox combobox-width ajax-combobox-referringProviderSpecialty'),
//                'required' => false,
//                'classtype' => 'referringProviderSpecialty'
//            ));
            $builder->add('userWrapperSpecialty', null, array(
                'label' => 'User Specialty:',
                'attr' => array('class'=>'combobox')
            ));

            $builder->add('userWrapperPhone', null, array(
                'label' => 'User Phone Number:',
                'attr' => array('class'=>'form-control')
            ));

            $builder->add('userWrapperEmail', null, array(
                'label' => 'User E-Mail:',
                'attr' => array('class'=>'form-control')
            ));
        }

        ///////////////// Many To Many relationship /////////////////

        //not editable: suites, rooms
        if( strtolower($this->mapper['className']) == strtolower("Department") ) {
            $builder->add('suites', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:SuiteList',
                'choice_label' => 'FullName',
                'label'=>'Suite(s):',
                'required'=> false,
                'multiple' => true,
                //'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width', 'readonly'=>true)
            ));

            $builder->add('rooms', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:RoomList',
                'choice_label' => 'FullName',
                'label'=>'Room(s):',
                'required'=> false,
                'multiple' => true,
                //'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width', 'readonly'=>true)
            ));
        }

        //Floor:
        //not editable: suites, rooms
        if( strtolower($this->mapper['className']) == strtolower("FloorList") ) {
            $builder->add('suites', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:SuiteList',
                'choice_label' => 'FullName',
                'label'=>'Suite(s):',
                'required'=> false,
                'multiple' => true,
                //'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width', 'readonly'=>true)
            ));

            $builder->add('rooms', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:RoomList',
                'choice_label' => 'FullName',
                'label'=>'Room(s):',
                'required'=> false,
                'multiple' => true,
                //'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width', 'readonly'=>true)
            ));
        }


        //Suite: buildings, floors
        if( strtolower($this->mapper['className']) == strtolower("SuiteList") ) {
            $builder->add('buildings', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:BuildingList',
                'label'=>'Building(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));

//            $builder->add('departments', EntityType::class, array(
//                'class' => 'OlegUserdirectoryBundle:Department',
//                'label'=>'Department(s):',
//                'required'=> false,
//                'multiple' => true,
//                //'by_reference' => false,
//                'attr' => array('class' => 'combobox combobox-width')
//            ));

            $builder->add('floors', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:FloorList',
                'label'=>'Floor(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));
        }

        //Room: buildings, suite
        if( strtolower($this->mapper['className']) == strtolower("RoomList") ) {
            $builder->add('buildings', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:BuildingList',
                'label'=>'Building(s):',
                'required'=> false,
                'multiple' => true,
                //'by_reference' => false,
                'attr' => array('class' => 'combobox combobox-width')
            ));

//            $builder->add('departments', EntityType::class, array(
//                'class' => 'OlegUserdirectoryBundle:Department',
//                'label'=>'Department(s):',
//                'required'=> false,
//                'multiple' => true,
//                'attr' => array('class' => 'combobox combobox-width')
//            ));

            $builder->add('suites', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:SuiteList',
                'choice_label' => 'FullName',
                'label'=>'Suite(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));

            $builder->add('floors', EntityType::class, array(
                'class' => 'OlegUserdirectoryBundle:FloorList',
                'label'=>'Floor(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));
        }

        ///////////////// EOF Many To Many relationship /////////////////






    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,   //$this->mapper['fullClassName'],
            'form_custom_value' => null,
            'form_custom_value_mapper' => null
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        $prefix = strtolower($this->mapper['className']);
        if( !$prefix ) {
            $prefix = 'genericlist';
        }
        return 'oleg_userdirectorybundle_'.$prefix;
    }
}
