<?php

namespace App\Form;

use App\Entity\Pole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RangeType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MonPoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pour les tracts, on garde peut-être un IntegerType car c'est précis ?
            // Mais si vous voulez une barre, voici comment faire :
            ->add('tract', RangeType::class, [
                'label' => 'Nombre de tracts',
                'attr' => [
                    'min' => 0,
                    'max' => 5000, // Adaptez le max selon vos besoins
                    'step' => 10,  // On avance de 10 en 10
                    'class' => 'form-range' // Classe Bootstrap pour faire joli
                ]
            ])
            
            // Pour l'affluence (0 à 10 par exemple, ou 0 à 100)
            ->add('afluence', RangeType::class, [
                'label' => 'Affluence (0 à 100)',
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'class' => 'form-range'
                ]
            ])

            // Pour les syndicats (Scores)
            ->add('unef', IntegerType::class)
            ->add('ue', IntegerType::class)
            ->add('uni', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pole::class,
        ]);
    }
}