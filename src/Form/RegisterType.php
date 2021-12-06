<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prènom',
                'constraints' => new Length( ['min' => 3, 'max' => 30]),
                'attr'  => [
                    'class' => ' font-italic',
                    'placeholder' => 'Saisir votre prènom..'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'constraints' => new Length( ['min' => 3, 'max' => 30]),
                'attr'  => [
                    'class' => ' font-italic',
                    'placeholder' => 'Saisir votre nom..'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr'  => [
                    'class' => ' font-italic',
                    'placeholder' => 'Saisir votre email..'
                ]
            ])

            // Deux champs input Password afin de comparer si ils sont identiques
            ->add('password', RepeatedType::class, [
                'constraints' => new Length( ['min' => 8, 'max' => 30]),
                'type' => PasswordType::class,
                'invalid_message' => 'Les password doivent être identique',
                'label' => 'Votre password',
                'required' => 'true',
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                    'placeholder' => 'Saisir le password',
                    'class' => ' font-italic'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez le password',
                    'attr' => [
                    'placeholder' => 'Confirmez le password',
                    'class' => ' font-italic'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
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
