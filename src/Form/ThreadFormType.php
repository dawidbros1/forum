<?php

namespace App\Form;

use App\Entity\Thread;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThreadFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('name', TextType::class, [
                'label' => "Nazwa wątka",
                'label_attr' => [
                    'class' => "fw-bold",
                ],
                'attr' => [
                    'placeholder' => "Wprowadź nazwę wątka",
                    "rows" => 6,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => "Opis wątka",
                'label_attr' => [
                    'class' => "fw-bold",
                ],
                'attr' => [
                    'placeholder' => "Wprowadź opis wątka",
                    "rows" => 6,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Thread::class,
        ]);
    }
}
