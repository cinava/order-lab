<?php

namespace Oleg\VacReqBundle\Form;


use Oleg\UserdirectoryBundle\Entity\PrivateComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class VacReqRequestType extends AbstractType
{

    protected $params;

    public function __construct( $params )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('user', new FellAppUserType($this->params), array(
            'data_class' => 'Oleg\UserdirectoryBundle\Entity\User',
            'label' => false,
            'required' => false,
        ));

        $builder->add('startDate','date',array(
            'widget' => 'single_text',
            'label' => "Start Date:",
            'format' => 'MM/dd/yyyy',  //'MM/dd/yyyy, H:mm:ss',
            'attr' => array('class' => 'datepicker form-control fellapp-startDate'),
            'required' => false,
        ));

        $builder->add('endDate','date',array(
            'widget' => 'single_text',
            'label' => "End Date:",
            'format' => 'MM/dd/yyyy',
            'attr' => array('class' => 'datepicker form-control fellapp-endDate'),
            'required' => false,
        ));

        ///////////////////////// tree node /////////////////////////
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $title = $event->getData();
            $form = $event->getForm();

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

            $form->add('institution', 'employees_custom_selector', array(
                'label' => $label,
                'required' => false,
                //'attr' => array('class' => 'ajax-combobox-institution', 'type' => 'hidden'),
                'attr' => array(
                    'class' => 'ajax-combobox-compositetree',
                    'type' => 'hidden',
                    'data-compositetree-bundlename' => 'UserdirectoryBundle',
                    'data-compositetree-classname' => 'Institution'
                ),
                'classtype' => 'institution'
            ));
        });
        ///////////////////////// EOF tree node /////////////////////////

        $builder->add('status', 'choice', array(
            'disabled' => ($this->params['admin'] ? true : false),
            'choices'   => array(
                'pending' => 'pending',
                'approved' => 'approved',
                'declined' => 'declined'
            ),
            'label' => "Status:",
            'required' => true,
            'attr' => array('class' => 'combobox combobox-width'),
        ));


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\VacReqBundle\Entity\VacReqRequest',
        ));
    }

    public function getName()
    {
        return 'oleg_vacreqbundle_request';
    }
}
