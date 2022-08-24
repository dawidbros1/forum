<?php

namespace App\Form;

use App\Entity\Topic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('title', TextType::class, [
                'label' => "Nazwa tematu",
                'label_attr' => [
                    'class' => "fw-bold",
                ],
                'attr' => [
                    'placeholder' => "Wprowadź nazwę tematu",
                ],
                'row_attr' => [
                    'class' => "mb-2 border p-2",
                ],
            ])
            ->add('text', TextareaType::class, [
                'label' => "Treść tematu",
                'label_attr' => [
                    'class' => "fw-bold",
                ],
                'attr' => [
                    'placeholder' => "Wprowadź treść tematu",
                    "rows" => 6,
                ],
                'row_attr' => [
                    'class' => "mb-2 border p-2",
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}
