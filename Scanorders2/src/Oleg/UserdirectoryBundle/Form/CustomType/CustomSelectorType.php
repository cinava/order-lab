<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/13/13
 * Time: 5:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Form\CustomType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

use Oleg\UserdirectoryBundle\Form\DataTransformer\StringTransformer;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;

class CustomSelectorType extends AbstractType {

    /**
     * @var ObjectManager
     * @var SecurityContext
     */
    private $om;
    private $sc;
    private $serviceContainer;

     /**
     * @param ObjectManager $om
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, SecurityContext $sc, $serviceContainer = null)
    {
        $this->om = $om;
        $this->sc = $sc;
        $this->serviceContainer = $serviceContainer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $username = $this->sc->getToken()->getUser();
        
        $classtype = $options['classtype'];
         
        switch( $classtype ) {
            case "institution":
                $transformer = new GenericTreeTransformer($this->om, $username, 'Institution');
                break;
            case "department":
                $transformer = new GenericTreeTransformer($this->om, $username, 'Department');
                break;
            case "division":
                $transformer = new GenericTreeTransformer($this->om, $username, 'Division');
                break;
            case "service":
                $transformer = new GenericTreeTransformer($this->om, $username, 'Service');
                break;
            default:
                $transformer = new StringTransformer($this->om, $username);
        }
        
        
        $builder->addModelTransformer($transformer);        
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selection does not exist',
        ));
        
        $resolver->setRequired(array(
            'classtype',
        ));

//        $resolver->setAllowedTypes(array(
//            'classtype' => 'Doctrine\Common\Persistence\ObjectManager',
//        ));
        
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'employees_custom_selector';
    }


}