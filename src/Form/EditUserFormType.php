<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'mapped' => false,
                'required' => false,
                'empty_data' => $options['data']->getUsername(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a username',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your username should be at least {{ limit }} characters',
                        'max' => 20,
                        'maxMessage' => 'Your username should be at most {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'mapped' => false,
                'required' => false,
                'empty_data' => $options['data']->getEmail(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Your email should be at least {{ limit }} characters',
                        'max' => 60,
                        'maxMessage' => 'Your email should be at most {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('mood', TextType::class, [
                'mapped' => false,
                'required' => false,
                'empty_data' => $options['data']->getMood(),
                'constraints' => [
                    new Length([
                        'max' => 60,
                        'maxMessage' => 'Your mood should be at most {{ limit }} characters',
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
