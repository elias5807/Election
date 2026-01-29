<?php

namespace App\Form;

use App\Entity\Stand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; // <--- Changement ici

class MonStandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lait', CheckboxType::class, [
                'label' => 'Lait en stock',
                'required' => false, // Important pour les checkbox
            ])
            ->add('oeuf', CheckboxType::class, [
                'label' => 'Oeufs en stock',
                'required' => false,
            ])
            ->add('rhum', CheckboxType::class, [
                'label' => 'Rhum en stock',
                'required' => false,
            ])
            ->add('farine', CheckboxType::class, [
                'label' => 'Farine en stock',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stand::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token', 
            'csrf_token_id'   => 'stand_item',
        ]);
    }
}