<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('email')
            // ->add('firstName')
            // ->add('lastName')
            // ->add('address')
            // ->add('postal')
            // ->add('city')
            // ->add('telephone')
            // ->add('birthDate', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('firstName', TextType::class, [
                'label' => 'form.first_name',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prénom'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom'],
            ])
            ->add('birthDate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Date de naissance'],
                'format' => 'dd-MM-yyyy',
            ])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Adresse'],
                'label' => 'Adresse',
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('postal', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Code postal'],
                'label' => 'Code postal',
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('telephone', TextType::class, [
                'error_bubbling' => true,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^0[0-9]*$/',
                        'message' => 'Le nombre doit commencer par un zéro',
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Téléphone'],
                'label' => 'Téléphone',
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ville'],
                'label' => 'Ville',
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('email', TextType::class, [
                'error_bubbling' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Email'],
                'label' => 'Email',
                'label_attr' => ['class' => 'form-label'],
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
