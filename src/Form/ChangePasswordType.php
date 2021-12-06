<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'disabled' => true,
            'label' => 'Mon prènom :'
        ])
        ->add('lastname', TextType::class, [
            'disabled' => true,
            'label' => 'Mon nom :'
        ])
        ->add('email', EmailType::class, [
            'disabled' => true,
            'label' => 'Mon email :'
        ])
        ->add('old_password', PasswordType::class, [
            'label' => 'Password actuel :',
            'mapped' => false,
            'attr' => [
                'class' => " font-italic",
                'placeholder' => "Saisir votre password actuel..."
            ]
        ])
        // Deux champs input Password afin de comparer si ils sont identiques
        ->add('new_password', RepeatedType::class, [
            'mapped' => false,
            'constraints' => new Length( ['min' => 8, 'max' => 30]),
            'type' => PasswordType::class,
            'invalid_message' => 'Les password doivent être identique',
            'label' => 'Mon nouveau password',
            'required' => 'true',
            'first_options' => [
                'label' => 'Mon nouveau password',
                'attr' => [
                'placeholder' => 'Saisir le nouveau password',
                'class' => ' font-italic'
                ]
            ],
            'second_options' => [
                'label' => 'Confirmez le nouveau password',
                'attr' => [
                'placeholder' => 'Confirmez le nouveau password',
                'class' => ' font-italic'
                ]
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => "Modifier",
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
