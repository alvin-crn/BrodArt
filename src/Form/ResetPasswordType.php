<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe ne sont pas identiques.',
            'help' => 'Le mot de passe doit faire entre 12 et 25 caractères.',
            'constraints' => new Length([
                'min' => 12,
                'max' => 25,
                'minMessage' => "Votre mot de passe doit faire au minimum 12 caractères",
                'maxMessage' => "Votre mot de passe doit faire au maximum 25 caractères"
            ]),
            'required' => true,
            'first_options' => [
                'label' => 'Nouveau mot de passe',
                'attr' => [
                    'placeholder' => '•••••••••'
                ]
            ],
            'second_options' => [
                'label' => 'Confirmez votre nouveau mot de passe',
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
            // Configure your form options here
        ]);
    }
}
