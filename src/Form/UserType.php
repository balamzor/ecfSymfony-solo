<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Adresse e-mail',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Abonné' => 'ROLE_SUBSCRIBED',
                ],
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-check-input d-flex flex-row mb-3',
                ],
            ])
            ->add('password', null, [
                'label' => false,
                'attr' => ['class' => 'form-control', 'style' => 'display:none;'],
            ])
            ->add('firstName', null, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lastName', null, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('address', null, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('postal', null, [
                'label' => 'Code postal',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('telephone', null, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('birthDate', null, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control mb-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
