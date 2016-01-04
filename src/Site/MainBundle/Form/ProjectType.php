<?php

namespace Site\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('onMain', 'choice', array(
                'required' => true,
                'label' => 'backend.project.on_main',
                'choices' => array(
                    false => 'Не показывать на главной',
                    true => 'Показывать на главной'
                )
            ))
            ->add('title', 'text', array(
                'required' => true,
                'label' => 'backend.project.title'
            ))
            ->add('site', 'url', array(
                'required' => true,
                'label' => 'backend.project.site'
            ))
            ->add('metaTitle', 'text', array(
                'required' => false,
                'label' => 'backend.project.metaTitle'
            ))
            ->add('metaDescription', 'textarea', array(
                'required' => false,
                'label' => 'backend.project.metaDescription'
            ))
            ->add('metaKeywords', 'text', array(
                'required' => false,
                'label' => 'backend.project.metaKeywords'
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'backend.project.description'
            ))
            ->add('text', 'textarea', array(
                'required' => false,
                'label' => 'backend.project.text',
                "attr" => array(
                    "class" => "ckeditor"
                )
            ))
            ->add('file', 'file', array(
                'required' => false,
                'label' => 'backend.project.img'
            ))
            ->add('gallery', 'file', array(
                'required' => false,
                'label' => 'backend.project.images',
                'attr' => array(
                    'class' => 'uploadify',
                    'multiple' => true
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
            'data_class' => 'Site\MainBundle\Entity\Project',
            'translation_domain' => 'menu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_mainbundle_project';
    }
}
