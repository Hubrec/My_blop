<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a comment',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your comment should be at least {{ limit }} characters',
                        'max' => 300,
                        'maxMessage' => 'Your comment should be at most {{ limit }} characters',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
