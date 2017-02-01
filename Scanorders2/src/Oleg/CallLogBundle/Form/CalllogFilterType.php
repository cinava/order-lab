<?php

namespace Oleg\CallLogBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalllogFilterType extends AbstractType
{

    private $params;

    public function __construct( $params=null )
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('startDate', 'datetime', array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'Start Date'), //'title'=>'Start Year', 'data-toggle'=>'tooltip',
        ));

        $builder->add('endDate', 'datetime', array(
            'label' => false,
            'widget' => 'single_text',
            'required' => false,
            'format' => 'MM/dd/yyyy',
            'attr' => array('class'=>'datepicker form-control submit-on-enter-field', 'placeholder'=>'End Date'), //'title'=>'End Year', 'data-toggle'=>'tooltip',
        ));

        //echo "def=".$this->params['messageCategoryDefault']."<br>";
        //print_r($this->params['messageCategories']);
        $builder->add('messageCategory', 'choice', array(
            'label' => false,
            'required' => false,
            'choices' => $this->params['messageCategories'],
            //'data' => $this->params['messageCategoryDefault'],
            'attr' => array('class' => 'combobox submit-on-enter-field', 'placeholder' => "Message Type"),
        ));

//        $builder->add('mrntype', 'custom_selector', array(
//            'label'=>'MRN Type:',
//            'required' => true,
//            //'multiple' => false,
//            //'data' => 4,
//            'data' => $this->params['mrntype'],
//            'attr' => array('class' => 'ajax-combobox combobox combobox-width mrntype-combobox mrntype-exception-autogenerated'),
//            'classtype' => 'mrntype'
//        ));
        $builder->add('mrntype', 'entity', array(
            'class' => 'OlegOrderformBundle:MrnType',
            'label' => false,
            'required' => true,
            'data' => $this->params['mrntype'],
            'attr' => array('class' => 'combobox combobox-no-width', 'placeholder' => "MRN Type", 'style'=>'width:50%;'),
        ));
        $builder->add('search', 'text', array(
            //'max_length'=>200,
            'required'=>false,
            'label' => false,
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder' => "MRN or Last Name", 'style'=>'width:50%; float:right; height:28px;'),
        ));

        $builder->add('author', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'property' => 'getUsernameOptimal',
            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Author"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->leftJoin("u.infos", "infos")
                    ->leftJoin("u.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->andWhere("(employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL)")
                    ->andWhere("(u.testingAccount = 0 OR u.testingAccount IS NULL)")
                    ->andWhere("(u.keytype IS NOT NULL AND u.primaryPublicUserId != 'system')")
                    ->orderBy("infos.displayName","ASC");
                //->where('u.roles LIKE :roles OR u=:user')
                //->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user']));
            },
        ));

//        $builder->add('referringProvider', 'entity', array(
//            'class' => 'OlegUserdirectoryBundle:User',
//            'label' => false,
//            'required' => false,
//            'property' => 'getUsernameOptimal',
//            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Referring Provider"),
//            'query_builder' => function (EntityRepository $er) {
//                return $er->createQueryBuilder('u')
//                    ->leftJoin("u.infos", "infos")
//                    ->leftJoin("u.employmentStatus", "employmentStatus")
//                    ->leftJoin("employmentStatus.employmentType", "employmentType")
//                    ->andWhere("(employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL)")
//                    ->andWhere("(u.testingAccount = 0 OR u.testingAccount IS NULL)")
//                    ->andWhere("(u.keytype IS NOT NULL AND u.primaryPublicUserId != 'system')")
//                    ->orderBy("infos.displayName","ASC");
//                //->where('u.roles LIKE :roles OR u=:user')
//                //->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user']));
//            },
//        ));
//        $builder->add('referringProvider', 'custom_selector', array(
//            'label' => false,
//            'attr' => array('class' => 'combobox combobox-width ajax-combobox-encounterReferringProvider', 'placeholder' => "Referring Provider"),
//            'required' => false,
//            'classtype' => 'singleUserWrapper'
//            //'classtype' => 'userWrapper'
//        ));
        $builder->add('referringProvider', 'choice', array(
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Referring Provider"),
            'choices' => $this->params['referringProviders']
        ));

        $builder->add('referringProviderSpecialty', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:HealthcareProviderSpecialtiesList',
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Specialty"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy("u.orderinlist","ASC");
            },
        ));

        $builder->add('encounterLocation', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:Location',
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Location"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->leftJoin("u.locationTypes", "locationTypes")
                    ->where("locationTypes.name='Encounter Location'")
                    ->orderBy("u.name","ASC");
            },
        ));

        $builder->add('messageStatus', 'choice', array(
            'label' => false,
            'required' => false,
            'attr' => array('class' => 'combobox', 'placeholder' => "Message Status"),
            'choices' => $this->params['messageStatuses']
        ));

        //Patient List
        $builder->add('patientListTitle', 'entity', array(
            'class' => 'OlegOrderformBundle:PatientListHierarchy',
            'label' => false,
            'required' => false,
            'property' => 'name',    //'getNodeNameWithParent',
            'attr' => array('class' => 'combobox', 'placeholder' => "Patient List"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where("u.level = 3")
                    ->orderBy("u.orderinlist","ASC");
            },
        ));

        //Entry Body
        $builder->add('entryBodySearch', 'text', array(
            'required'=>false,
            'label' => false,
            'attr' => array('class'=>'form-control submit-on-enter-field', 'placeholder' => "Entry Body"),
        ));

        //Attending
        $builder->add('attending', 'entity', array(
            'class' => 'OlegUserdirectoryBundle:User',
            'label' => false,
            'required' => false,
            'property' => 'getUsernameOptimal',
            'attr' => array('class' => 'combobox combobox-width', 'placeholder' => "Attending"),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->leftJoin("u.infos", "infos")
                    ->leftJoin("u.employmentStatus", "employmentStatus")
                    ->leftJoin("employmentStatus.employmentType", "employmentType")
                    ->andWhere("(employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL)")
                    ->andWhere("(u.testingAccount = 0 OR u.testingAccount IS NULL)")
                    ->andWhere("(u.keytype IS NOT NULL AND u.primaryPublicUserId != 'system')")
                    ->orderBy("infos.displayName","ASC");
                //->where('u.roles LIKE :roles OR u=:user')
                //->setParameters(array('roles' => '%' . 'ROLE_SCANORDER_ORDERING_PROVIDER' . '%', 'user' => $this->params['user']));
            },
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'filter';
    }
}
