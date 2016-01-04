<?php

namespace Site\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartnersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', null, array(
                'required' => false,
                'label' => 'backend.partners.position',
                'attr' => array(
                    'min' => 0
                )
            ))
            ->add('name', null, array(
                'required' => true,
                'label' => 'backend.partners.name'
            ))
            ->add('link', 'url', array(
                'required' => true,
                'label' => 'backend.partners.link',
                'attr' => array(
                    'placeholder' => 'http://'
                )
            ))
            ->add('file', 'file', array(
                'required' => false,
                'label' => 'backend.partners.img'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\MainBundle\Entity\Partners',
            'translation_domain' => 'menu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_mainbundle_partners';
    }
}
