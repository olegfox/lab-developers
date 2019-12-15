<?php

namespace Site\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'required' => true,
                'label' => 'backend.service.title'
            ))
            ->add('date', null, array(
                'required' => true,
                'label' => 'backend.service.date'
            ))
            ->add('metaTitle', 'text', array(
                'required' => false,
                'label' => 'backend.service.metatitle'
            ))
            ->add('metaDescription', 'textarea', array(
                'required' => false,
                'label' => 'backend.service.metadescription'
            ))
            ->add('metaKeywords', 'text', array(
                'required' => false,
                'label' => 'backend.service.metakeywords'
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'backend.service.description'
            ))
            ->add('text', 'textarea', array(
                'required' => false,
                'label' => 'backend.service.text',
                "attr" => array(
                    "class" => "ckeditor"
                )
            ))
            ->add('file', 'file', array(
                'required' => false,
                'label' => 'backend.service.img'
            ))
            ->add('onMain', 'choice', array(
                'required' => true,
                'label' => 'backend.project.on_main',
                'choices' => array(
                    false => 'Не показывать на главной',
                    true => 'Показывать на главной'
                )
            ))
            ->add('price', null, array(
                'required' => false,
                'label' => 'backend.project.price'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\MainBundle\Entity\Service',
            'translation_domain' => 'menu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_mainbundle_service';
    }
}
