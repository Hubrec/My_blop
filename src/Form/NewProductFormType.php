<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class NewProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your title should be at least {{ limit }} characters',
                        'max' => 60,
                        'maxMessage' => 'Your title should be at most {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Your post should have a description',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your description should be at least {{ limit }} characters',
                        'max' => 120,
                        'maxMessage' => 'Your description should be at most {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Your post should have some content',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your content should be at least {{ limit }} characters',
                        'max' => 4096,
                        'maxMessage' => 'Your content should be at most {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('categories', ChoiceType::class, [
                'mapped' => false,
                'multiple' => true,
                'choices' => array_combine($options['data'], $options['data']),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Your post should have at least one category',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
