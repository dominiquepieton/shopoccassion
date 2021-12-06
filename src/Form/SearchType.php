<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class, [
                'label' => 'recherche par Nom :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre recherche...',
                    'class' => ' font-italic form-control-sm'
                ] 
            ])
            ->add('categories', EntityType::class, [
                'label' => 'recherche par catègorie :',
                'required' => false,
                'class' => Categorie::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-outline-secondary'
                ]
            ])
        ; 
    }
    
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false
        ]);
    }


    public function getBlockPrefix()
    {
        return '';
    }
}