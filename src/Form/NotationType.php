<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Notation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ],
                'label' => "Note",
                'attr' => [
                    'class' => 'form-select form-select-sm',
                    'style' => 'width: 5em;' // Adjust width to fit two digits
                ]
            ])
            ->add('comment', TextareaType::class, [
                'label' => "Votre commentaire",
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3
                ]
            ])
            ->add('date', null, [
                'label' => false,
                'widget' => 'single_text',
                'attr' => [
                    'hidden' => true
                ]
            ])
            ->add('idBook', EntityType::class, [
                'class' => Book::class,
                'label' => false,
                'choice_label' => 'id',
                'attr' => [
                    'hidden' => true
                ]
            ])
            ->add('userId', EntityType::class, [
                'class' => User::class,
                'label' => false,
                'choice_label' => 'id',
                'attr' => [
                    'hidden' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notation::class,
        ]);
    }
}

