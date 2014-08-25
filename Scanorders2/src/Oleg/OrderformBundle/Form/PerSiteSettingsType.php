<?php

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PerSiteSettingsType extends AbstractType
{

    protected $user;
    protected $services;

    public function __construct( $user = null, $services = null )
    {
        $this->user = $user;
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if( $this->user == null ) {

            $builder->add( 'siteName', 'text', array(
                'label'=>'Site Name:',
                'required'=>false,
                'read_only'=>true,
                'attr' => array('class'=>'form-control')
            ));

            $builder->add( 'permittedInstitutionalPHIScope', null, array(
                'label'=>'Permitted Institutional PHI Scope:',
                'required'=>false,
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width')
            ));

            $builder->add( 'scanOrdersServicesScope', null, array(
                'label'=>'Scan Orders Services Scope:',
                'required'=>false,
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width')
            ));

            $builder->add( 'chiefServices', null, array(
                'label'=>'Chief Services:',
                'required'=>false,
                'multiple' => true,
                'attr' => array('class'=>'combobox combobox-width')
            ));

            $builder->add( 'defaultService', null, array(
                'label'=>'Default Services:',
                'required'=>false,
                'attr' => array('class'=>'combobox combobox-width')
            ));

        } else {

//            $builder->add( 'permittedInstitutionalPHIScope', 'entity', array(
//                'class' => 'OlegOrderformBundle:PerSiteSettings',
//                //'property' => 'permittedInstitutionalPHIScope',
//                'label'=>'Institution:',
//                'required'=> true,
//                'multiple' => false,
//                'attr' => array('class'=>'combobox combobox-width'), //combobox-institution
//                'query_builder' => function(EntityRepository $er) {
//                        return $er->createQueryBuilder('i')
//                            ->innerJoin('i.user', 'user')
//                            ->where("user = :user AND i.siteName = :sitename")
//                            ->setParameters( array(
//                                'user' => $this->user,
//                                'sitename' => 'scanorder',
//                            ));
//                    },
//            ));

        }


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\PerSiteSettings',
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_persitesettings';
    }
}
