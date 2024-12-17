<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Subscriptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'data' => '',
                'attr' => [
                    'class' => 'form-control input-group-sm',
                    'required' => true,
                ],
                'label_attr' => [
                    'class' => 'input-group-text',
                ],
                'mapped' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'data' => '',
                'attr' => [
                    'class' => 'form-control input-group-sm',
                    'required' => true,
                ],
                'label_attr' => [
                    'class' => 'input-group-text',
                ],
                'mapped' => false,
            ])
            ->add('cbNumbers', TextType::class, [
                'label' => 'Carte bleue VISA',
                'attr' => [
                    'class' => 'form-control input-group-sm',
                    'id' => 'ccn',
                    'type' => 'tel',
                    'inputmode' => 'numeric',
                    'pattern' => '[0-9\s]{13,19}',
                    'autocomplete' => 'cc-number',
                    'maxlength' => '19',
                    'placeholder' => 'XXXX XXXX XXXX XXXX',
                    'required' => true,
                ],
                'label_attr' => [
                    'class' => 'input-group-text',
                ],
                'mapped' => false,
            ])
            ->add('cbDate', DateType::class, [
                'label' => 'Date de validité',
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control input-group-sm',
                    'type' => 'date',
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'max' => '2035-01-01',
                    'placeholder' => 'Date de validité',
                ],
                'label_attr' => [
                    'class' => 'input-group-text',
                ],
                'mapped' => false,
            ])
            ->add('cbVerif', TextType::class, [
                'label' => 'Code de sécurité',
                'attr' => [
                    'class' => 'form-control input-group-sm',
                    'type' => 'tel',
                    'maxlength' => '3',
                ],
                'label_attr' => [
                    'class' => 'input-group-text',
                ],
                'mapped' => false,
            ])
            // ->add('submit', SubmitType::class, [
            //     'label' => 'Effectuer le paiement',
            //     'attr' => [
            //         'class' => 'btn btn-primary w-100',
            //     ],

            // ])
            ->add('dateDebut', null, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => ['style' => 'display:none'],
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => ['style' => 'display:none'],
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'label' => false,
                'attr' => ['style' => 'display:none'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscriptions::class,
        ]);
    }
}
