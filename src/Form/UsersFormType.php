<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',TextType::class, options:[
                'required' => false, // It's not required for editing
                'attr' => [
                    'class' => 'form-control'
                ]])
            ->add('password', PasswordType::class, [
                'mapped' => false, // The field won't be mapped to the entity
                'required' => false, // It's not required for editing
                'attr' => [
                    'class' => 'form-control'
                ]
            ])            
            ->add('name',TextType::class, options:[
                'required' => false, // It's not required for editing
                'attr' => [
                    'class' => 'form-control'
                ]])
            ->add('address',TextType::class, options:[
                'required' => false, // It's not required for editing
                'attr' => [
                    'class' => 'form-control'
                ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
