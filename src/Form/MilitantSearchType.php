<?php
// src/Form/MilitantSearchType.php
namespace App\Form;

use App\Entity\Pole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MilitantSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Rechercher un nom ou prénom...']
            ])
            ->add('pole', EntityType::class, [
                'class' => Pole::class,
                'choice_label' => 'nom', // ou le nom de l'attribut libellé dans Pole
                'required' => false,
                'label' => false,
                'placeholder' => 'Tous les pôles'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET', // Important pour que la recherche soit partageable via URL
            'csrf_protection' => false,
        ]);
    }
}