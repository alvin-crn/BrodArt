<?php 

namespace App\Form;

use App\Service\SearchService;
use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Size;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{
    function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher...'
                ]
            ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('couleurs', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Color::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('tailles', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Size::class,
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }   

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchService::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    function getBlockPrefix()
    {
        return '';
    }
}