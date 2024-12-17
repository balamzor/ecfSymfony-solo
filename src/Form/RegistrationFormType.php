<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                'data' => new \DateTime('-18 years'),
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
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'error_bubbling' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation.',
                    ]),
                ],
                'attr' => ['class' => 'form-check-input'],
                'label' => 'J\'accepte les conditions d\'utilisation',
                'label_attr' => ['class' => 'form-check-label'],
            ])
            // ->add('plainPassword', PasswordType::class, [
            //     // instead of being set onto the object directly,
            //     // this is read and encoded in the controller
            //     'mapped' => false,
            //     'error_bubbling' => true,
            //     'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control', 'placeholder' => 'Mot de passe'],
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Choisissez un mot de passe',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères',
            //             // max length allowed by Symfony for security reasons
            //             'max' => 4096,
            //         ]),
            //         // new Assert\Callback([
            //         //     'callback' => function ($object, ExecutionContextInterface $context) {
            //         //         $form = $context->getRoot();

            //         //         $plainPassword = $form->get('plainPassword')->getData();
            //         //         $confirmPassword = $form->get('confirmPassword')->getData();

            //         //         if ($plainPassword !== $confirmPassword) {
            //         //             $context
            //         //                 ->buildViolation('Les mots de passe ne sont pas identiques')
            //         //                 ->atPath('confirmPassword')
            //         //                 ->addViolation();
            //         //         }
            //         //     },
            //         // ]),
            //     ],
            //     'label' => 'form.password',

            //     'label_attr' => ['class' => 'form-label'],
            // ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'error_bubbling' => true,
                'type' => PasswordType::class,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'first_options' => [
                    'label' => 'Mot de passe',
                    'label_attr' => ['class' => 'form-label'],
                    'attr' => ['class' => 'form-control'],

                ],
                'second_options' => [
                    'label' => 'Confirmation',
                    'label_attr' => ['class' => 'form-label'],
                    'attr' => ['class' => 'form-control'],

                ],
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
