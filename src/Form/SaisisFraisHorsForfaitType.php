<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisisFraisHorsForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date de la dépense',
                'attr' => [
                    'placeholder' => 'Date de la dépense'
                ]
            ])
            ->add('montant', IntegerType::class, [
                'label' => 'Montant de la dépense',
                'attr' => [
                    'placeholder' => 'Montant de la dépense'
                ]
            ])
            ->add('motif', TextType::class, [
                'label' => 'Motif de la dépense',
                'attr' => [
                    'placeholder' => 'Motif de la dépense'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}