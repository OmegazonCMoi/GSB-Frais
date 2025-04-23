<?php
// src/Form/SelectFicheComptableType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectFicheFraisComptableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mois', ChoiceType::class, [
                'label' => 'Mois',
                'choices' => $this->getMonthChoices(),
                'required' => true,
            ])
            ->add('annee', ChoiceType::class, [
                'label' => 'Année',
                'choices' => $this->getYearChoices(),
                'required' => true,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'label' => 'Utilisateur',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher'
            ]);
    }

    private function getYearChoices(): array
    {
        $currentYear = (int)date('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 15; $i--) {
            $years[$i] = $i;
        }
        return $years;
    }

    private function getMonthChoices(): array
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[str_pad($i, 2, '0', STR_PAD_LEFT)] = $i;
        }
        return $months;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}