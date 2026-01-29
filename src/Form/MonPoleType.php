<?php

namespace App\Form;

use App\Entity\Pole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RangeType; 
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MonPoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tract', RangeType::class, [
                'label' => 'Nombre de tracts',
                'required' => false, // <--- IMPORTANT pour l'auto-save
                'attr' => [
                    'min' => 0,
                    'max' => 5000,
                    'step' => 10,
                    'class' => 'form-range'
                ]
            ])
            
            ->add('afluence', RangeType::class, [
                'label' => 'Affluence (0 à 100)',
                'required' => false, // <--- IMPORTANT
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'class' => 'form-range'
                ]
            ])

            // Pour les syndicats, on met required false pour ne pas bloquer
            // si l'utilisateur supprime la valeur avant d'en écrire une nouvelle
            ->add('unef', IntegerType::class, [
                'required' => false, 
                'label' => 'Score UNEF'
            ])
            ->add('ue', IntegerType::class, [
                'required' => false,
                'label' => 'Score UE'
            ])
            ->add('uni', IntegerType::class, [
                'required' => false,
                'label' => 'Score UNI'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pole::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token', 
            'csrf_token_id'   => 'pole_item', // Un ID unique pour ce formulaire
        ]);
    }
}