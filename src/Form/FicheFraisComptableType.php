<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheFraisComptableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputStyle = 'block w-full p-2 border border-neutral-700 rounded-lg shadow-sm bg-neutral-800 text-neutral-100 focus:ring focus:ring-blue-500 focus:outline-none';
        $labelStyle = 'block text-sm font-medium text-neutral-200';

        $builder
            ->add('nbJustificatifs', null, [
                'label' => 'Nombre de justificatifs : ',
                'attr' => [
                    'placeholder' => 'Nombre de justificatifs',
                    'class' => $inputStyle
                ],
                'label_attr' => [
                    'class' => $labelStyle
                ]
            ])
            ->add('montantValid', null, [
                'label' => 'Montant validé : ',
                'attr' => [
                    'placeholder' => 'Montant validé en euros',
                    'class' => $inputStyle
                ],
                'label_attr' => [
                    'class' => $labelStyle
                ]
            ])
            ->add('dateModif', null, [
                'widget' => 'single_text',
                'label' => 'Date de modification : ',
                'data' => new \DateTime('now', new \DateTimeZone('Europe/Paris')),
                'attr' => [
                    'class' => $inputStyle
                ],
                'label_attr' => [
                    'class' => $labelStyle
                ]
            ])
            ->add('Etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'libelle',
                'label' => 'État : ',
                'placeholder' => 'Sélectionner un état :',
                'attr' => [
                    'class' => $inputStyle
                ],
                'label_attr' => [
                    'class' => $labelStyle
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer les modifications',
                'attr' => [
                    'class' => 'w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mt-4'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FicheFrais::class,
        ]);
    }
}
