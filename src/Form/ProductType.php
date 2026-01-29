<?php

namespace App\Form;

use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une catégorie',
            ])
            ->add('color', EntityType::class, [
                'class' => Color::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une couleur',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('subtitle', TextType::class, [
                'required' => false,
                'label' => 'Sous-titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('illustration', FileType::class, [
                'required' => !$options['edit'],
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration2', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration3', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration4', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration5', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration6', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration7', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration8', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration9', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('illustration10', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => $this->imageConstraints(),
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
            ])
            ->add('promo', MoneyType::class, [
                'label' => 'Prix promotionnel',
                'required' => false,
            ])
            ->add('deliveryCost', MoneyType::class, [
                'label' => 'Frais de livraison',
            ])
            ->add('isBest', CheckboxType::class, [
                'required' => false,
                'label' => 'Meilleure vente ?',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'edit' => false,
        ]);

        $resolver->setAllowedTypes('edit', 'bool');
    }

    private function imageConstraints(): array
    {
        return [
            new File([
                'maxSize' => '5M',
                'mimeTypes' => ['image/*'],
                'mimeTypesMessage' => 'Veuillez uploader une image valide de 5Mo maximum.',
            ]),
        ];
    }
}
