<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'label' => false
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'constraints' => new Length([
                    'min' => 12,
                    'max' => 25,
                    'minMessage' => "Votre mot de passe doit faire au minimum 12 caractères",
                    'maxMessage' => "Votre mot de passe doit faire au maximum 25 caractères"
                ]),
                'required' => true,
                'first_options' => [
                    'label' => 'Nouveau mot de passe :',
                    'attr' => [
                        'placeholder' => '•••••••••'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez le nouveau mot de passe :',
                    'attr' => [
                        'placeholder' => '•••••••••'
                    ]
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

    }
}
