<?php

namespace App\Form;

use App\Entity\Etat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectEtatFicheFraisComptableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'libelle',
                'label' => 'État de la fiche :',
                'placeholder' => 'Sélectionner un état',
                'required' => true,
                'attr' => [
                    'class' => 'w-full p-2 border border-neutral-300 dark:border-neutral-700 rounded-md bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100'
                ],
                'row_attr' => [
                    'class' => 'mb-4',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'w-full bg-neutral-600 dark:bg-neutral-500 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-neutral-700 dark:hover:bg-neutral-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mt-4'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
