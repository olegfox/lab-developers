<?php

namespace Site\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsType extends AbstractType
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
                'label' => 'backend.news.title'
            ))
            ->add('date', null, array(
                'required' => true,
                'label' => 'backend.news.date'
            ))
            ->add('metaTitle', 'text', array(
                'required' => false,
                'label' => 'backend.news.metatitle'
            ))
            ->add('metaDescription', 'textarea', array(
                'required' => false,
                'label' => 'backend.news.metadescription'
            ))
            ->add('metaKeywords', 'text', array(
                'required' => false,
                'label' => 'backend.news.metakeywords'
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'backend.news.description'
            ))
            ->add('text', 'textarea', array(
                'required' => false,
                'label' => 'backend.news.text',
                "attr" => array(
                    "class" => "ckeditor"
                )
            ))
            ->add('file', 'file', array(
                'required' => false,
                'label' => 'backend.news.img'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\MainBundle\Entity\News',
            'translation_domain' => 'menu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_mainbundle_news';
    }
}
