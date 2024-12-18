<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MonthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

            $builder->add('mois', ChoiceType::class, [
                'choices' => $options['fiches'],
                'placeholder' => 'Choose a month',
                'choice_label' => function ($choice) {
                    return $choice->getMois()->format('F Y'); // Format d'affichage
                },
                'choice_value' => function ($choice) {
                    return $choice ? $choice->getId() : ''; // Assure-toi que l'id est bien accessible
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'fiches' => [],
        ]);
    }
}