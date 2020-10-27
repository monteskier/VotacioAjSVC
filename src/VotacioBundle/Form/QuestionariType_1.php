<?php

namespace VotacioBundle\Form;
use VotacioBundle\Entity\Pregunta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class QuestionariType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titol')
            ->add('file')
            ->add('preguntes' ,CollectionType::class, array(
                    'entry_type'   => PreguntaType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'prototype_name' =>'pregunta__text__',
                    'by_reference' => false,
                    ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VotacioBundle\Entity\Questionari'
        ));
    }
    public function getName(){
        return "Questionaris";
    }
}
