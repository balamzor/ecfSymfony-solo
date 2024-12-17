<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Workspace;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class WorkspaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, [
                'row_attr' => ['class' => 'custom-spacing'],
                'label' => 'Nom',
            ])
            ->add('capacity', IntegerType::class, [
                'row_attr' => ['class' => 'custom-spacing'],
                'label' => 'Capacité',
            ])
            ->add('image', FileType::class, [
                'row_attr' => ['class' => 'custom-spacing'],
                'mapped' => false,
                'required' => false,
                'label' => 'Image',
            ])
            ->add('equipment', EntityType::class, [
                'class' => Equipment::class,
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => true,
                'row_attr' => ['class' => 'custom-spacing'],
                'label' => 'Équipements disponibles',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workspace::class,
        ]);
    }
}
