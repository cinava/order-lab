<?php

namespace Oleg\UserdirectoryBundle\Form;

use Doctrine\ORM\EntityRepository;
use Oleg\UserdirectoryBundle\Entity\CompositeNodeInterface;
use Oleg\UserdirectoryBundle\Entity\Institution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GenericListType extends AbstractType
{

    protected $params;
    protected $mapper;

    public function __construct( $params, $mapper )
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

        $builder->add('list', new ListType($this->params, $this->mapper), array(
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
                'property' => 'getTreeName',
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

//            $builder->add('institution','entity',array(
//                'class' => 'OlegUserdirectoryBundle:Institution',
//                'label' => "Institution:",
//                'property' => "getTreeName",
//                'multiple' => false,
//                'attr' => array('class'=>'combobox combobox-width'),
//                'required' => false,
//            ));

            $builder->add( 'institution', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:Institution',
                'property' => 'getTreeName',
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
                'read_only' => true,
                'attr' => array('class'=>'form-control'),
            ));
        }
        if( method_exists($this->params['entity'],'getLft') ) {
            $builder->add( 'lft', null, array(
                'label'=>'Left:',
                'read_only' => true,
                'attr' => array('class'=>'form-control'),
            ));
        }
        if( method_exists($this->params['entity'],'getRgt') ) {
            $builder->add( 'rgt', null, array(
                'label'=>'Right:',
                'read_only' => true,
                'attr' => array('class'=>'form-control'),
            ));
        }


        if( method_exists($this->params['entity'],'getInstitutions') ) {
            //echo "add institutions <br>";
            $builder->add( 'institutions', 'entity', array(
                'class' => 'OlegUserdirectoryBundle:Institution',
                'property' => 'getTreeName',
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

                $form->add( 'collaborationInstitutions', 'entity', array(
                    'class' => 'OlegUserdirectoryBundle:Institution',
                    'property' => 'getTreeName',
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

                $form->add( 'collaborationType', 'entity', array(
                    'class' => 'OlegUserdirectoryBundle:CollaborationTypeList',
                    'property' => 'name',
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

//            $builder->add( 'collaborations', 'entity', array(
//                'class' => 'OlegUserdirectoryBundle:Collaboration',
//                //'read_only' => true,
//                //'property' => 'getTreeName',
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
//            $builder->add( 'collaborationType', 'entity', array(
//                'class' => 'OlegUserdirectoryBundle:CollaborationTypeList',
//                'property' => 'name',
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

        //PlatformListManagerRootList
        if( method_exists($this->params['entity'],'getListId') && method_exists($this->params['entity'],'getListName') ) {
            $builder->add( 'listId', null, array(
                'label'=>'List ID:',
                'attr' => array('class'=>'form-control'),
            ));
            $builder->add( 'listName', null, array(
                'label'=>'List Name:',
                'attr' => array('class'=>'form-control'),
            ));
            $builder->add( 'listRootName', null, array(
                'label'=>'List Root Name:',
                'attr' => array('class'=>'form-control'),
            ));
        }

        //PermissionList
        if( strtolower($this->mapper['className']) == strtolower("PermissionList") ) {
            $builder->add('permissionObjectList','entity',array(
                'class' => 'OlegUserdirectoryBundle:PermissionObjectList',
                'label' => "Object:",
                'multiple' => false,
                'required' => false,
                'attr' => array('class'=>'combobox'),
            ));
            $builder->add('permissionActionList','entity',array(
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
            $builder->add('attributes','entity',array(
                'class' => 'OlegUserdirectoryBundle:RoleAttributeList',
                'label' => "Attribute(s):",
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width'),
                'required' => false,
            ));

            //permissions: show list of
            $builder->add('permissions', 'collection', array(
                'type' => new PermissionType($this->params),
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__permissions__',
            ));

//            $builder->add('sites','entity',array(
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

            $builder->add('fellowshipSubspecialty','entity',array(
                'class' => 'OlegUserdirectoryBundle:FellowshipSubspecialty',
                'label' => "FellowshipSubspecialty:",
                'multiple' => false,
                'required' => false,
                'attr' => array('class'=>'combobox combobox-width')
            ));
        }

        if( method_exists($this->params['entity'],'getSites') ) {
            $builder->add('sites','entity',array(
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
        if( strtolower($this->mapper['className']) == strtolower("OrganizationalGroupType") ||
            strtolower($this->mapper['className']) == strtolower("CommentGroupType") ||
            strtolower($this->mapper['className']) == strtolower("ResearchGroupType") ||
            strtolower($this->mapper['className']) == strtolower("CourseGroupType")
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
                'read_only' => true,
                'attr' => array('class' => 'form-control')
            ));
            //always read only - do not allow to change parent
            $builder->add('parent',null,array(
                'label' => $this->mapper['parentClassName'].' (Parent):',
                'property' => 'getTreeName',
                'attr' => array('class' => 'combobox combobox-width', 'readonly'=>'readonly')
            ));
        }



        ///////////////// Many To Many relationship /////////////////

        //not editable: suites, rooms
        if( strtolower($this->mapper['className']) == strtolower("Department") ) {
            $builder->add('suites','entity',array(
                'class' => 'OlegUserdirectoryBundle:SuiteList',
                'property' => 'FullName',
                'label'=>'Suite(s):',
                'required'=> false,
                'multiple' => true,
                'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));

            $builder->add('rooms','entity',array(
                'class' => 'OlegUserdirectoryBundle:RoomList',
                'property' => 'FullName',
                'label'=>'Room(s):',
                'required'=> false,
                'multiple' => true,
                'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));
        }

        //Floor:
        //not editable: suites, rooms
        if( strtolower($this->mapper['className']) == strtolower("FloorList") ) {
            $builder->add('suites','entity',array(
                'class' => 'OlegUserdirectoryBundle:SuiteList',
                'property' => 'FullName',
                'label'=>'Suite(s):',
                'required'=> false,
                'multiple' => true,
                'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));

            $builder->add('rooms','entity',array(
                'class' => 'OlegUserdirectoryBundle:RoomList',
                'property' => 'FullName',
                'label'=>'Room(s):',
                'required'=> false,
                'multiple' => true,
                'disabled' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));
        }


        //Suite: buildings, floors
        if( strtolower($this->mapper['className']) == strtolower("SuiteList") ) {
            $builder->add('buildings','entity',array(
                'class' => 'OlegUserdirectoryBundle:BuildingList',
                'label'=>'Building(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));

//            $builder->add('departments','entity',array(
//                'class' => 'OlegUserdirectoryBundle:Department',
//                'label'=>'Department(s):',
//                'required'=> false,
//                'multiple' => true,
//                //'by_reference' => false,
//                'attr' => array('class' => 'combobox combobox-width')
//            ));

            $builder->add('floors','entity',array(
                'class' => 'OlegUserdirectoryBundle:FloorList',
                'label'=>'Floor(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));
        }

        //Room: buildings, suite
        if( strtolower($this->mapper['className']) == strtolower("RoomList") ) {
            $builder->add('buildings','entity',array(
                'class' => 'OlegUserdirectoryBundle:BuildingList',
                'label'=>'Building(s):',
                'required'=> false,
                'multiple' => true,
                //'by_reference' => false,
                'attr' => array('class' => 'combobox combobox-width')
            ));

//            $builder->add('departments','entity',array(
//                'class' => 'OlegUserdirectoryBundle:Department',
//                'label'=>'Department(s):',
//                'required'=> false,
//                'multiple' => true,
//                'attr' => array('class' => 'combobox combobox-width')
//            ));

            $builder->add('suites','entity',array(
                'class' => 'OlegUserdirectoryBundle:SuiteList',
                'property' => 'FullName',
                'label'=>'Suite(s):',
                'required'=> false,
                'multiple' => true,
                'attr' => array('class' => 'combobox combobox-width')
            ));

            $builder->add('floors','entity',array(
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->mapper['fullClassName']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oleg_userdirectorybundle_'.strtolower($this->mapper['className']);
    }
}
