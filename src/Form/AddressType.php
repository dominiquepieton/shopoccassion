<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom voulez-vous donner à votre adresse ?',
                'attr' => [
                'placeholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                'placeholder' => 'Entrez votre '
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                'placeholder' => 'Entrez votre nom'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Votre entreprise',
                'attr' => [
                'placeholder' => 'Entrez votre entreprise'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Entrez votre adresse',
                'attr' => [
                'placeholder' => 'Entrez votre adresse'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Entrez votre code postal',
                'attr' => [
                'placeholder' => 'Entrez votre code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Entrez votre ville',
                'attr' => [
                'placeholder' => 'Entrez votre ville'
                ]
            ])
            ->add('pays', TextType::class, [
                'label' => 'Entrez votre pays',
                'attr' => [
                'placeholder' => 'Entrez votre pays'
                ]    
            ])
            ->add('phone', TextType::class, [
                'label' => 'Entrez votre téléphone',
                'attr' => [
                'placeholder' => 'Entrez votre téléphone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-outline-primary'
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
