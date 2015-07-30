<?php

namespace Oleg\OrderformBundle\Form;

use Oleg\UserdirectoryBundle\Form\UserWrapperType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class ResearchType extends AbstractType
{

    protected $entity;
    protected $params;

    public function __construct( $params=null, $entity=null )
    {
        $this->params = $params;
        $this->entity = $entity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        ///////////////////////// tree node /////////////////////////
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $title = $event->getData();
            $form = $event->getForm();

            $label = null;
            $mapper = array(
                'prefix' => "Oleg",
                'className' => "ProjectTitleTree",
                'bundleName' => "OrderformBundle",
                'organizationalGroupType' => "ResearchGroupType"
            );
            if( $title ) {
                $projectTitle = $title->getProjectTitle();
                if( $projectTitle ) {
                    $label = $this->params['em']->getRepository('OlegOrderformBundle:ProjectTitleTree')->getLevelLabels($projectTitle,$mapper) . ":";
                }
            }
            if( !$label ) {
                $label = $this->params['em']->getRepository('OlegOrderformBundle:ProjectTitleTree')->getLevelLabels(null,$mapper) . ":";
            }
            //echo "label=".$label."<br>";

            $form->add('projectTitle', 'custom_selector', array(
                'label' => $label,
                'required' => false,
                'attr' => array(
                    'class' => 'ajax-combobox-compositetree combobox-research-projectTitle',
                    'type' => 'hidden',
                    'data-compositetree-bundlename' => 'OrderformBundle',
                    'data-compositetree-classname' => 'ProjectTitleTree',
                    'data-compositetree-initnode-function' => 'getOptionalUserResearch'
                ),
                'classtype' => 'projectTitle'
            ));
        });
        ///////////////////////// EOF tree node /////////////////////////



        //Display fields for Data Review
        if( $this->params['type'] == 'SingleObject' ) {


            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $holder = $event->getData();
                $form = $event->getForm();

                if( !$holder ) {
                    return;
                }

                ///////////////////// userWrappers /////////////////////
                $criterion = "user.roles LIKE '%ROLE_SCANORDER_PRINCIPAL_INVESTIGATOR%'";

                //add all users from UserWrappers for this research
                foreach( $holder->getUserWrappers() as $userWrapper ) {
                    if( $userWrapper->getUser() && $userWrapper->getUser()->getId() ) {
                        $criterion = $criterion . " OR " . "user.id=" . $userWrapper->getUser()->getId();
                    }
                }

                $this->params['user.criterion'] = $criterion;   //array('role'=>'ROLE_SCANORDER_PRINCIPAL_INVESTIGATOR');

                $this->params['name.label'] = 'Principal Investigator (as entered by user for this order):';
                $this->params['user.label'] = 'Principal Investigator:';

                $form->add('userWrappers', 'collection', array(
                    'type' => new UserWrapperType($this->params),
                    'label' => false,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                    'prototype_name' => '__userwrapper__',
                ));
                ///////////////////// EOF userWrappers /////////////////////


                ///////////////////// primaryPrincipal /////////////////////
                $principalArr = array();
                $userWrappers = array();
                $comment = '';
                if( $holder ) {
                    $userWrappers = $holder->getUserWrappers();

                    //create array of choices: 'choices' => array("OPTION1"=>"TEXT1", "OPTION2"=>"TEXT2", "OPTION3"=>"TEXT3"),
                    foreach( $userWrappers as $userWrapper ) {
                        //echo $principal."<br>";
                        $principalArr[$userWrapper->getId()] = $userWrapper->getName();
                    }

                    if( $holder->getPrimarySet() ) {
                        $comment = ' for this order';
                    }
                }

                $form->add( 'primaryPrincipal', 'entity', array(
                    'class' => 'OlegUserdirectoryBundle:UserWrapper',
                    'label'=>'Primary Principal Investigator (as entered by user'.$comment.'):',
                    'required'=> false,
                    'multiple' => false,
                    'attr' => array('class'=>'combobox combobox-width'),
                    'choices' => $userWrappers
                ));
                ///////////////////// EOF primaryPrincipal /////////////////////





            });


        } else {
            //TODO: add mask: comma is not allowed
            $builder->add('userWrappers', 'custom_selector', array(
                'label' => 'Principal Investigator(s):',
                'attr' => array('class' => 'combobox combobox-width combobox-optionaluser-research', 'type' => 'hidden'),
                'required'=>false,
                'classtype' => 'optionalUserResearch'
            ));

        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\Research'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_researchtype';
    }
}
