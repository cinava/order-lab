<?php

namespace Oleg\TranslationalResearchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ReviewBaseType extends AbstractType
{

    protected $params;
    private $data_class;
    //private $disabledReviewers;

    public function formConstructor( $params )
    {
        $this->params = $params;


    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formConstructor($options['form_custom_value']);
        $this->data_class = $options['data_class'];

//        $this->disabledReviewers = true;
//        if( $this->params['standAlone'] === false ) {
//            $this->disabledReviewers = false;
//        }
//        if( $this->params['admin'] ) {
//            $this->disabledReviewers = false;
//        }

        $builder->add( 'id', HiddenType::class, array(
            'label'=>false,
            'required'=>false,
            //'attr' => array('class' => 'comment-field-id')
        ));

        //echo "add reviewer object <br>";

        //$builder->add('assignment');

        //////////////////////// reviewer //////////////////////////
        //Visible only to admins and reviewer
        //Not Visible to requester
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $reviewObjectEntity = $event->getData();
            $form = $event->getForm();

            if(!$reviewObjectEntity) {
                //new review object
                $form->add('reviewer', null, array(
                    'label' => "Reviewer:",
                    'disabled' => $this->params['disabledReviewers'],  //$this->disabledReviewers,
                    'attr' => array('class' => 'combobox combobox-width') //, 'readonly'=>true
                ));
                $form->add('reviewerDelegate', null, array(
                    'label' => "Reviewer Delegate:",
                    'disabled' => $this->params['disabledReviewers'],
                    'attr' => array('class' => 'combobox combobox-width') //, 'readonly'=>true
                ));
                return;
            }

            //Show reviewers only for admin, primary reviewer and the logged in user
//            if(
//                $this->params['admin'] ||
//                $this->params['transresUtil']->isProjectReviewer($this->params['user'],array($reviewObjectEntity))
//            ) {
//                //ok
//            } else {
//                //don't show
//                return;
//            }

            if(
                $this->params['admin'] ||
                $this->params['transresUtil']->isProjectReviewer($this->params['user'],array($reviewObjectEntity))
            ) {
                //existing review object
                $form->add('reviewer', null, array(
                    'label' => "Reviewer:",
                    'disabled' => $this->params['disabledReviewers'],
                    'attr' => array('class' => 'combobox combobox-width') //, 'readonly'=>true
                ));
                $form->add('reviewerDelegate', null, array(
                    'label' => "Reviewer Delegate:",
                    'disabled' => $this->params['disabledReviewers'],
                    'attr' => array('class' => 'combobox combobox-width') //, 'readonly'=>true
                ));
            }

        });

        //////////////////////// EOF reviewer //////////////////////////

//        if( 0 ) {
//            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//
//                $reviewEntity = $event->getData();
//                $form = $event->getForm();
//
//                if (!$reviewEntity) {
//                    //exit("reviewEntity is NULL <br>");
//                    return null;
//                }
//
//                $disabledReviewerFields = true;
//                if ($this->params['admin']) {
//                    $disabledReviewerFields = false;
//                }
//                if ($this->params['user']->getId() == $reviewEntity->getReviewer()->getId()) {
//                    $disabledReviewerFields = false;
//                }
//                if (
//                    $reviewEntity->getReviewerDelegate() &&
//                    $this->params['user']->getId() == $reviewEntity->getReviewerDelegate()->getId()
//                ) {
//                    $disabledReviewerFields = false;
//                }
//
//                //Reviewer's field
//                $approved = 'Approved';
//                $rejected = 'Rejected';
//                if ($this->params["stateStr"] == "committee_review") {
//                    $approved = 'Like';
//                    $rejected = 'Dislike';
//                }
//
//                $form->add('decision', ChoiceType::class, array(
//                    'choices' => array(
//                        $approved => 'approved',
//                        $rejected => 'rejected',
//                        'Pending' => null
//                    ),
//                    'invalid_message' => 'invalid value: decision',
//                    //'choices_as_values' => true,
//                    'disabled' => $disabledReviewerFields,
//                    'label' => "Decision:",
//                    'multiple' => false,
//                    'expanded' => true,
//                    'attr' => array('class' => 'horizontal_type')
//                ));
//
//                $form->add('comment', TextareaType::class, array(
//                    'label' => 'Comment:',
//                    'disabled' => $disabledReviewerFields,
//                    'required' => false,
//                    'attr' => array('class' => 'textarea form-control'),
//                ));
//
////                $form->add('reviewedBy', null, array(
////                    'label' => "Reviewed By:",
////                    'disabled' => true,
////                    'attr' => array('class'=>'combobox combobox-width') //, 'readonly'=>true
////                ));
//
//            });
//        }
//        if( 0 ) {
//            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//
//                $reviewEntity = $event->getData();
//                $form = $event->getForm();
//
//                if (!$reviewEntity) {
//                    //exit("reviewEntity is NULL <br>");
//                    //return null;
//                }
//
//                $decisions = array();
//
//                if( $this->params["stateStr"] == "irb_review" || $this->params["stateStr"] == "admin_review" ) {
//                    $decisions = array(
//                        'Approved' => 'approved',
//                        'Rejected' => 'rejected',
//                        'Request additional information from submitter' => 'missinginfo',  //'Pending additional information from submitter',
//                        'Pending' => null
//                    );
//                }
//                if( $this->params["stateStr"] == "committee_review" ) {
//                    //echo "primaryReview=".$this->params["review"]."<br>";//TODO: review is null?
//                    if( $this->params["review"] && $this->params["review"]->getPrimaryReview() === true ) {
//                        $decisions = array(
//                            'Approved' => 'approved',
//                            'Rejected' => 'rejected',
//                            'Pending' => null
//                        );
//                    } else {
//                        $decisions = array(
//                            'Like' => 'approved',
//                            'Dislike' => 'rejected',
//                            'Pending' => null
//                        );
//                    }
//                }
//                if( $this->params["stateStr"] == "final_review" ) {
//                    $decisions = array(
//                        'Approved' => 'approved',
//                        'Rejected' => 'rejected',
//                        'Pending' => null
//                    );
//                }
//
//                $disabledReviewerFields = true;
//                if( $this->params["disabledReviewerFields"] == false ) {
//                    $disabledReviewerFields = false;
//                }
//
//                $form->add('decision', ChoiceType::class, array(
//                    'choices' => $decisions,
//                    'invalid_message' => 'invalid value: decision',
//                    //'choices_as_values' => true,
//                    'disabled' => $disabledReviewerFields,
//                    //'disabled' => true,
//                    'label' => "Decision:",
//                    'multiple' => false,
//                    'expanded' => true,
//                    'attr' => array('class' => 'horizontal_type')
//                ));
//
//                $form->add('comment', TextareaType::class, array(
//                    'label' => 'Comment:',
//                    'disabled' => $disabledReviewerFields,
//                    'required' => false,
//                    'attr' => array('class' => 'textarea form-control'),
//                ));
//
//                if( $this->params['stateStr'] == "committee_review" ) {
//                    //echo "show primaryReview <br>";
//                    $form->add('primaryReview', CheckboxType::class, array(
//                        'label' => 'Primary Review:',
//                        'required' => false,
//                        'attr' => array('class' => 'form-control', 'style' => 'margin:0'),
//                    ));
//                }
//
//            });
//        }
        if(1){
            $decisions = array();

            //echo "data_class=".$this->data_class."<br>";

            //if( $this->params["stateStr"] == "irb_review" || $this->params["stateStr"] == "admin_review" ) {
            if(
                $this->data_class == 'Oleg\\TranslationalResearchBundle\\Entity\\IrbReview' ||
                $this->data_class == 'Oleg\\TranslationalResearchBundle\\Entity\\AdminReview'
            ) {
                $decisions = array(
                    'Approved' => 'approved',
                    'Rejected' => 'rejected',
                    'Pending additional information from submitter' => 'missinginfo',  //'Pending additional information from submitter',
                    'Pending Review' => null
                );
            }
            //if( $this->params["stateStr"] == "committee_review" ) {
            if( $this->data_class == 'Oleg\\TranslationalResearchBundle\\Entity\\CommitteeReview' ) {
                //echo "primaryReview=".$this->params["review"]."<br>";//TODO: review is null?
                if( $this->params["review"] && $this->params["review"]->getPrimaryReview() === true ) {
                    $decisions = array(
                        'Approved' => 'approved',
                        'Rejected' => 'rejected',
                        'Pending Review' => null
                    );
                } else {
                    $decisions = array(
                        'Approved/Approval Recommended' => 'approved',
                        'Rejected/Rejection Recommended' => 'rejected',
                        'Pending Review' => null
                    );
                }
            }
            //if( $this->params["stateStr"] == "final_review" ) {
            if( $this->data_class == 'Oleg\\TranslationalResearchBundle\\Entity\\FinalReview' ) {
                $decisions = array(
                    'Approved' => 'approved',
                    'Rejected' => 'rejected',
                    'Pending Review' => null
                );
            }

            $disabledReviewerFields = true;
            if( $this->params["disabledReviewerFields"] == false ) {
                $disabledReviewerFields = false;
            }

            $builder->add('decision', ChoiceType::class, array(
//                'choices' => array(
//                    $approved => 'approved',
//                    $rejected => 'rejected',
//                    'Pending' => null
//                ),
                'choices' => $decisions,
                'invalid_message' => 'invalid value: decision',
                //'choices_as_values' => true,
                'disabled' => $disabledReviewerFields,
                //'disabled' => true,
                'label' => "Decision:",
                'multiple' => false,
                'expanded' => true,
                'attr' => array('class' => 'horizontal_type')
            ));

//            $builder->add('comment', TextareaType::class, array(
//                'label' => 'Comment:',
//                'disabled' => $disabledReviewerFields,
//                'required' => false,
//                'attr' => array('class' => 'textarea form-control'),
//            ));
//            $builder->add('comments', CollectionType::class, array(
//                'entry_type' => FosCommentType::class,
//                'entry_options' => array(
//                    'data_class' => 'Oleg\UserdirectoryBundle\Entity\FosComment',
//                    'form_custom_value' => $this->params
//                ),
//                'label' => false,
//                'required' => false,
//                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false,
//                'prototype' => true,
//                'prototype_name' => '__comments__',
//            ));

            if( $this->params['stateStr'] == "committee_review" ) {
                //echo "show primaryReview <br>";
                $builder->add('primaryReview', CheckboxType::class, array(
                    'label' => 'Primary Reviewer:',
                    'required' => false,
                    'disabled' => $this->params['disabledReviewers'],
                    'attr' => array('class' => 'form-control', 'style' => 'margin:0'),
                ));
            }
        }

//        //Reviewer's field
//        $approved = 'Approved';
//        $rejected = 'Rejected';
//        if( $this->params["stateStr"] == "committee_review" ) {
//            $approved = 'Like';
//            $rejected = 'Dislike';
//        }
//
//        $builder->add('decision', ChoiceType::class, array(
//            'choices' => array(
//                $approved => 'approved',
//                $rejected => 'rejected'
//            ),
//            'invalid_message' => 'invalid value: decision',
//            //'choices_as_values' => true,
//            'disabled' => $disabledReviewerFields,
//            'label' => "Decision:",
//            'multiple' => false,
//            'expanded' => true,
//            'attr' => array('class' => 'horizontal_type')
//        ));
//
//        $builder->add('comment', TextareaType::class, array(
//            'label'=>'Comment:',
//            'disabled' => $disabledReviewerFields,
//            'required'=> false,
//            'attr' => array('class'=>'textarea form-control'),
//        ));




    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'form_custom_value' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oleg_translationalresearchbundle_review';
    }


}
