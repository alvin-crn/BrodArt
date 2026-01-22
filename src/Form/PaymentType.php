<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('card_name', TextType::class, [
        'label' => 'Nom sur la carte',
        'mapped' => false,
        'constraints' => [
          new NotBlank(),
        ],
      ])
      ->add('card_number', TextType::class, [
        'label' => 'Numéro de carte',
        'mapped' => false,
        'attr' => [
          'maxlength' => 19,
          'placeholder' => '1234 5678 9012 3456'
        ],
        'constraints' => [
          new NotBlank(),
        ],
      ])
      ->add('card_expiry', TextType::class, [
        'label' => 'Date d’expiration',
        'mapped' => false,
        'attr' => [
          'placeholder' => 'MM/AA'
        ],
        'constraints' => [
          new NotBlank(),
        ],
      ])
      ->add('card_cvc', TextType::class, [
        'label' => 'CVC',
        'mapped' => false,
        'attr' => [
          'maxlength' => 4,
          'placeholder' => '123'
        ],
        'constraints' => [
          new NotBlank(),
        ],
      ])
      ->add('accept_cgv', CheckboxType::class, [
        'label' => 'J’accepte les conditions générales de vente',
        'mapped' => false,
        'required' => true,
        'row_attr' => [
          'class' => 'form-cgv',
        ],
      ]);
  }
}
