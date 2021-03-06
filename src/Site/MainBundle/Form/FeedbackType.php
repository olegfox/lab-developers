<?php

namespace Site\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'required' => true,
                'label' => false,
                'attr' => array(
                    'placeholder' => 'frontend.feedback.placeholder.name'
                )
            ))
            ->add('phone', 'text', array(
                'required' => true,
                'label' => false,
                'attr' => array(
                    'placeholder' => 'frontend.feedback.placeholder.phone'
                )
            ))
            ->add('email', 'email', array(
                'required' => true,
                'label' => false,
                'attr' => array(
                    'placeholder' => 'frontend.feedback.placeholder.email'
                )
            ))
            ->add('message', 'textarea', array(
                'required' => true,
                'label' => false,
                'attr' => array(
                    'placeholder' => 'frontend.feedback.placeholder.message'
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\MainBundle\Form\Feedback',
            'translation_domain' => 'menu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}
