<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom de mon adresse :',
            'required' => true,
            'attr' => [
                'placeholder' => 'Domicile'
            ]
        ])
        ->add('firstname', TextType::class, [
            'label' => 'Mon prénom :',
            'required' => true,
            'attr' => [
                'placeholder' => 'John'
            ]
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Mon nom :',
            'required' => true,
            'attr' => [
                'placeholder' => 'Doe'
            ]
        ])
        ->add('compagny', TextType::class, [
            'label' => 'Ma société :',
            'required' => false,
            'empty_data' => null,
            'attr' => [
                'placeholder' => '(Facultatif) Nom de votre société'
            ]
        ])
        ->add('address', TextType::class, [
            'label' => 'Mon adresse :',
            'required' => true,
            'attr' => [
                'placeholder' => '8 rue des lylas...'
            ]
        ])
        ->add('postal', TextType::class, [
            'label' => 'Code postal :',
            'required' => true,
            'attr' => [
                'placeholder' => '75010'
            ]
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville :',
            'required' => true,
            'attr' => [
                'placeholder' => 'Paris'
            ]
        ])
        ->add('country', CountryType::class, [
            'label' => 'Pays : ',
            'required' => true,
        ])
        ->add('phone', TelType::class, [
            'label' => 'Téléphone :',
            'required' => true,
            'attr' => [
                'placeholder' => '0123456789'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
