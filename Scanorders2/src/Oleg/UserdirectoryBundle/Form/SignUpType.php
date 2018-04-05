<?php

namespace Oleg\UserdirectoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignUpType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('createdate')->add('updatedate')->add('userName')->add('email')->add('firstName')->add('lastName')->add('phone')->add('salt')->add('hashPassword')->add('registrationLinkID')->add('registrationStatus')->add('ip')->add('useragent')->add('width')->add('height')->add('user')->add('site')->add('updatedby')->add('institution')->add('administrativeTitle');

        $builder->add( 'userName', TextType::class, array(
            'label'=>'User Name:',
            //'required'=> true,
            'attr' => array('class'=>'form-control'),
        ));

        //password RepeatedType::class
        $builder->add( 'hashPassword', PasswordType::class, array(
            //'mapped' => false,
            'label'=>'Password:',
            'attr' => array('class' => 'form-control')
        ));

//        $builder->add('hashPassword', RepeatedType::class, array(
//            //'mapped' => false,
//            //'error_bubbling' => true,
//            'invalid_message' => 'Please make sure the passwords match',
//            'options' => array('attr' => array('class' => 'password-field form-control')),
//            //'required' => true,
//            'type' => PasswordType::class,
//            'first_options'  => array('label' => 'Password:', 'error_bubbling' => true),
//            'second_options' => array('label' => 'Repeat Password:'),
//            'error_bubbling' => true
//        ));

        $builder->add( 'email', EmailType::class, array(
            'label'=>'Email:',
            //'required'=> true, //does not work here
            'attr' => array('class'=>'form-control'), //form-control-modif email-mask
        ));

        $builder->add('submit', SubmitType::class, array(
            'label' => 'Sign Up',
            'attr' => array('class' => 'btn btn-primary') //'onclick'=>'transresValidateHandsonTable();'
        ));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\UserdirectoryBundle\Entity\SignUp'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oleg_userdirectorybundle_signup';
    }


}
