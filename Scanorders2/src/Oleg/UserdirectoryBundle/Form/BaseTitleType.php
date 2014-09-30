<?php

namespace Oleg\UserdirectoryBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class BaseTitleType extends AbstractType
{

    protected $params;

    public function __construct( $params )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('id','hidden',array('label'=>false));

        $builder->add( 'name', 'text', array(
            'label'=>$this->params['label'].' Title:',   //'Admnistrative Title:',
            'required'=>false,
            'attr' => array('class' => 'form-control')
        ));

        $builder->add('startDate', 'date', array(
            'label' => $this->params['label']." Title Start Date:",
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM-dd-yyyy',
            'attr' => array('class' => 'datepicker form-control allow-future-date'),
        ));

        $builder->add('endDate', 'date', array(
            'label' => $this->params['label']." Title End Date:",
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM-dd-yyyy',
            'attr' => array('class' => 'datepicker form-control allow-future-date'),
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
        $builder->add('priority', 'choice', array(
            'choices'   => array(
                '0'   => 'Primary',
                '1' => 'Secondary'
            ),
            'label' => $this->params['label']." Type:",
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width'),
        ));


        //institution. User should be able to add institution to administrative or appointment titles
        $builder->add('institution', 'employees_custom_selector', array(
            'label' => 'Institution:',
            'attr' => array('class' => 'ajax-combobox-institution', 'type' => 'hidden'),
            'required' => false,
            'classtype' => 'institution'
        ));

        //department. User should be able to add institution to administrative or appointment titles
        $builder->add('department', 'employees_custom_selector', array(
            'label' => "Department:",
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width ajax-combobox-department', 'type' => 'hidden'),
            'classtype' => 'department'
        ));

        //division. User should be able to add institution to administrative or appointment titles
        $builder->add('division', 'employees_custom_selector', array(
            'label' => "Division:",
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width ajax-combobox-division', 'type' => 'hidden'),
            'classtype' => 'division'
        ));

        //service. User should be able to add institution to administrative or appointment titles
        $builder->add('service', 'employees_custom_selector', array(
            'label' => "Service:",
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width ajax-combobox-service', 'type' => 'hidden'),
            'classtype' => 'service'
        ));

        $builder->add( 'effort', 'text', array(
            'label'=>'Percent Effort:',
            'required'=>false,
            'attr' => array('class' => 'form-control', "data-inputmask"=>"'mask': '[o]', 'repeat': 10, 'greedy' : false")
        ));

        //position, residencyTrack, fellowshipType for AppointmentTitle (Academic Appointment Title)
        if( $this->params['fullClassName'] == "Oleg\UserdirectoryBundle\Entity\AppointmentTitle" ) {
            $builder->add('position', 'choice', array(
                'choices'   => array(
                    'Resident'   => 'Resident',
                    'Fellow' => 'Fellow',
                    'Clinical Faculty' => 'Clinical Faculty',
                    'Research Faculty' => 'Research Faculty'
                ),
                'label' => "Position Type:",
                'required' => false,
                'attr' => array('class' => 'combobox combobox-width appointmenttitle-position-field', 'onchange'=>'positionTypeListener(this)'),
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
        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->params['fullClassName'],
        ));
    }

    public function getName()
    {
        return 'oleg_userdirectorybundle_'.$this->params['formname'];
    }
}
