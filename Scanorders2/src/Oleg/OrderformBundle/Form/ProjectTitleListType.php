<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 4/14/14
 * Time: 1:09 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class ProjectTitleListType extends AbstractType
{

    protected $entity;
    protected $params;

    public function __construct( $params=null, $entity=null )
    {
        $this->params = $params;
        $this->entity = $entity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        //echo "id=".$this->entity->getId()."<br>";
        //echo $this->entity;
        //echo "projectTitle id=".$this->entity->getProjectTitle()->getId()."<br>";
        $principals = $this->entity->getProjectTitle()->getPrincipals();

        //create array of choices: 'choices' => array("OPTION1"=>"TEXT1", "OPTION2"=>"TEXT2", "OPTION3"=>"TEXT3"),
        $principalArr = array();
        foreach( $principals as $principal ) {
            //echo $principal."<br>";
            $principalArr[$principal->getId()] = $principal->getName();
        }

        $builder->add('primaryPrincipal', 'choice', array(
            'required' => true,
            'label'=>'Primary Principal Investigator:',
            'attr' => array('class' => 'combobox combobox-width'),
            'choices' => $principalArr,
        ));

//            $builder->add('primaryPrincipal', 'entity', array(
//                'class' => 'OlegOrderformBundle:PIList',
//                'label'=>'Primary Principal Investigator(s):',
//                'required' => true,
//                'preferred_choices' => array($this->entity->getProjectTitle()->getPrincipal),
//                //'read_only' => true,    //not working => disable by twig
//                //'multiple' => true,
//                //'attr' => array('class'=>'form-control form-control-modif'),
//                'attr' => array('class' => 'combobox combobox-width'),
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('list')
//                        //->select("list.name as id, list.name as text");
//                        ->leftJoin("list.projectTitles","parents")
//                        ->where("parents.id = :id")
//                        ->setParameter('id', $this->entity->getProjectTitle()->getId());
//                },
//            ));


        $builder->add('principals', 'collection', array(
            'type' => new PrincipalType($this->params,$this->entity),
            'required' => false,
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oleg\OrderformBundle\Entity\ProjectTitleList'
        ));
    }

    public function getName()
    {
        return 'oleg_orderformbundle_projecttitlelisttype';
    }
}
