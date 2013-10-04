<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword as OldUserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserType extends AbstractType
{

    protected $cicle;
    protected $roleAdmin;

    public function __construct( $cicle = null, $roleAdmin = false )
    {
        $this->cicle = $cicle;
        $this->roleAdmin = $roleAdmin;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (class_exists('Symfony\Component\Security\Core\Validator\Constraints\UserPassword')) {
            $constraint = new UserPassword();
        } else {
            // Symfony 2.1 support with the old constraint class
            $constraint = new OldUserPassword();
        }

//        $disabled = "";
//        if( !$this->roleAdmin ) {
//            $disabled = "disabled='disabled'";
//        }

        $builder->add('username', null, array(
            'label' => 'Username',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('firstName', null, array(
            'label' => 'First Name',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('lastName', null, array(
            'label' => 'Last Name',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('email', 'email', array(
            'label' => 'Email',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('displayName', null, array(
            'label' => 'Display Name',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('title', null, array(
            'label' => 'Title',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('phone', null, array(
            'label' => 'Phone',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('fax', null, array(
            'label' => 'Fax',
            'attr' => array('class'=>'form-control form-control-modif')
        ));
        $builder->add('office', null, array(
            'label' => 'Office',
            'attr' => array('class'=>'form-control form-control-modif')
        ));


//        $builder->add('current_password', 'password', array(
//            'label' => 'form.current_password',
//            'translation_domain' => 'FOSUserBundle',
//            'mapped' => false,
//            'constraints' => $constraint,
//        ));

        $attr = array('class' => 'combobox combobox-width');

//        if($this->params['cicle'] == "" || $this->params['cicle'] == 'new' || $this->params['cicle'] == 'create' ) {
//            $attr = array('class' => 'ajax-combobox-pathservice', 'type' => 'hidden');    //new
//        } else {
            //$attr = array('class' => 'combobox combobox-width');    //show
//        }
//        $builder->add('pathologyServices', 'custom_selector', array(
//            'label' => 'Pathology Service:',
//            'attr' => $attr,
//            'required' => false,
//            'classtype' => 'pathologyService'
//        ));

        $builder->add('pathologyServices',null, array(
            'attr' => $attr
        ));

//        $builder->add('defaultPathService', 'text', array(
//            'label' => 'Default Pathology Service:',
//            'attr' => array('class'=>'form-control form-control-modif')
//        ));
        $builder->add('defaultPathService', 'entity', array(
            'class' => 'OlegOrderformBundle:PathServiceList',
            'label' => 'Default Pathology Service:',
            'property' => 'name',
//            'query_builder' => function(EntityRepository $er) {
//                return $er->createQueryBuilder('u')
//                    ->orderBy('u.username', 'ASC');
//            },
        ));

        $attr = array('class' => 'combobox combobox-width');
        if( $this->roleAdmin ) {
            $builder->add('roles', 'choice', array(
                'choices'   => array(
                    'ROLE_SUPER_ADMIN'   => 'ROLE_SUPER_ADMIN',
                    'ROLE_ADMIN'   => 'ROLE_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                ),
                'attr'=>$attr,
    //            'property_path' => false,
                'multiple'  => true,
            ));

            $builder->add('enabled', null, array(
                'label' => 'Enabled',
                'attr' => array('class'=>'form-control form-control-modif')
            ));
            $builder->add('locked', null, array(
                'label' => 'Locked',
                'attr' => array('class'=>'form-control form-control-modif')
            ));
            $builder->add('expired', null, array(
                'label' => 'Expired',
                'attr' => array('class'=>'form-control form-control-modif')
            ));
        }

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oleg_orderformbundle_user';
    }

}
