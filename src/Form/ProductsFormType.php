<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\Images;

use App\Form\ImageType;
use App\Repository\ImagesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, options:[
                'attr' => [
                    'class' => 'form-control'
                ]])
            ->add('brand', TextType::class, options:[
            'attr' => [
                'class' => 'form-control'
            ]])
            ->add('price', MoneyType::class, options:[
                'label' => 'Prix',
                'attr' => [
                    'class' => 'form-control'
                ],
                'divisor' => 100,
                'constraints' => [
                    new Positive(
                        message: 'Le prix ne peut être négatif'
                    )
                ]
                
            ])
            ->add('stock', options:[
                'label' => 'Unités en stock',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'form-control'
                ],
                'query_builder' => function(CategoriesRepository $cr){
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                }
            ])
            ->add('images',FileType::class,[
                'label'=>false,
                'multiple' =>true,
                'mapped'=>false,
                'required'=>false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
