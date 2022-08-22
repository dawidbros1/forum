<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('title', TextType::class, [
                'label' => "Temat",
                'label_attr' => [
                    'class' => "fw-bold",
                ],
                'attr' => [
                    'placeholder' => "Wprowadź temat posta",
                ],
                'row_attr' => [
                    'class' => "mb-2 border p-2",
                ],
            ])
            ->add('text', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Wprowadź treść posta",
                    "rows" => 6,
                ],
                'label' => "Treść",
                'label_attr' => [
                    'class' => "fw-bold",
                ],
                'row_attr' => [
                    'class' => "mb-2 border p-2",
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
