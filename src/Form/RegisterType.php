<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30,
                    'minMessage' => "Votre prénom doit faire au minimum 2 lettres",
                    'maxMessage' => "Votre prénom doit faire au maximum 30 lettres"
                ]),
                'required' => true,
                'attr' => [
                    'placeholder' => 'John'
                ]
            ])
            ->add('lastname', TextType::class, [
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30,
                    'minMessage' => "Votre nom doit faire au minimum 2 lettres",
                    'maxMessage' => "Votre nom doit faire au maximum 30 lettres"
                ]),
                'required' => true,
                'attr' => [
                    'placeholder' => 'Doe'
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => new Length([
                    'min' => 10,
                    'max' => 80,
                    'minMessage' => "Votre email doit faire au minimum 10 caractères",
                    'maxMessage' => "Votre email doit faire au maximum 80 caractères"
                ]),
                'required' => true,
                'attr' => [
                    'placeholder' => 'john.doe@exemple.fr'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'constraints' => new Length([
                    'min' => 12,
                    'max' => 25,
                    'minMessage' => "Votre mot de passe doit faire au minimum 12 caractères",
                    'maxMessage' => "Votre mot de passe doit faire au maximum 25 caractères"
                ]),
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => '•••••••••'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => '•••••••••'
                    ]
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
