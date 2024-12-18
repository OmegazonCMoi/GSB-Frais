<?php

namespace App\Form;

use App\Entity\FraisForfait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisisFraisForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('km', IntegerType::class, [
                'label' => 'Frais kilométrique',
                'attr' => [
                    'placeholder' => 'Nombre de kilomètres'
                ]
            ])
            ->add('nuitees', IntegerType::class, [
                'label' => 'Nuitées hôtel',
                'attr' => [
                    'placeholder' => 'Nombre de nuitées'
                ]
            ])
            ->add('repas', IntegerType::class, [
                'label' => 'Repas restaurant',
                'attr' => [
                    'placeholder' => 'Nombre de repas'
                ]
            ])
            ->add('etape', IntegerType::class, [
                'label' => 'Etape',
                'attr' => [
                    'placeholder' => 'Nombre d\'étapes'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
