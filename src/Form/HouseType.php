<?php

namespace App\Form;

use App\Entity\House;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('type', ChoiceType::class, [
                    'choices'  => [
                        'Gite' => 'Gite',
                        'Chambre d\'hote' => 'Chambre hote',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'placeholder' => false,
            ])
            ->add('peopleNumber', IntegerType::class)
            ->add('description', TextareaType::class)
            ->add('advantage',CollectionType::class, [
                // each entry in the array will be an "email" field
                'entry_type' => TextType::class]
            )
            ->add('send', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => House::class,
            'translation_domain' => 'forms'
        ]);
    }
}
