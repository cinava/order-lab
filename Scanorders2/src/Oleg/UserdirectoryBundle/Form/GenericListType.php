<?php

namespace Oleg\UserdirectoryBundle\Form;

use Oleg\UserdirectoryBundle\Entity\CompositeNodeInterface;
use Oleg\UserdirectoryBundle\Entity\Institution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                //'attr' => array('class' => 'combobox combobox-width')
                'attr' => $attr
            ));


        }

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
        }

        //Role Attributes
        if( strtolower($this->mapper['className']) == strtolower("RoleAttributeList") ) {
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
            strtolower($this->mapper['className']) == strtolower("CommentGroupType")
        ) {
            $builder->add('level',null,array(
                'label'=>'Level:',
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
