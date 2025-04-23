<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\LigneFraisHorsForfait;
use App\Form\FicheFraisComptableType;
use App\Form\SelectFicheFraisComptableType;
use App\Form\SelectEtatFicheFraisComptableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    #[IsGranted('ROLE_COMPTABLE')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SelectFicheFraisComptableType::class);
        $form->handleRequest($request);
        $formEtat = $this->createForm(SelectEtatFicheFraisComptableType::class);
        $formEtat->handleRequest($request);

        $valideValue = $entityManager->getRepository(FicheFrais::class)->createQueryBuilder('f')
            ->where('f.valide = :valide')
            ->setParameter('valide', true)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        if ($form->isSubmitted() && $form->isValid()) {


            $data = $form->getData();
            $mois = $form->get('mois')->getData();
            $annee = $form->get('annee')->getData();
            $user = $data['user'];

            $date = new \DateTimeImmutable("{$annee}-{$mois}-01");

            $valideValue = $entityManager->getRepository(FicheFrais::class)->createQueryBuilder('f')
                ->where('f.user = :user')
                ->andWhere('f.mois = :date')
                ->setParameter('user', $user)
                ->setParameter('date', $date)
                ->getQuery()
                ->getResult();
        }

        if ($formEtat->isSubmitted() && $formEtat->isValid()) {
            $etat = $formEtat->get('etat')->getData();
            $fiches = $etat->getFicheFrais();
        }

        return $this->render('comptable/index.html.twig', [
            'form' => $form->createView(),
            'formEtat' => $formEtat->createView(),
            'fiche_frais' => $valideValue,
            'fiches' => $fiches ?? null,
        ]);
    }

    #[Route('/fiche/frais/{id}', name: 'app_comptable_info_fiche', methods: ['GET', 'POST'])]
    public function show(FicheFrais $ficheFrais, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FicheFraisComptableType::class, $ficheFrais);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $ficheFrais->setDateModif(new \DateTimeImmutable());

            $entityManager->persist($ficheFrais);
            $entityManager->flush();

            $this->addFlash('success', 'Données sauvegardées avec succès.');
            return $this->redirectToRoute('app_comptable_info_fiche', [
                'id' => $ficheFrais->getId(),
            ]);
        }

        return $this->render('comptable/info.html.twig', [
            'form' => $form->createView(),
            'fiche_frais' => $ficheFrais,
        ]);
    }

    #[Route('/fiche/update/{id}', name: 'app_comptable_fiche_update', methods: ['POST'])]
    public function updateToValidate(LigneFraisHorsForfait $lfhf, EntityManagerInterface $entityManager): Response
    {
        $lfhf->setIsValidate(!$lfhf->getIsValidate());

        $libelle = $lfhf->getLibelle();

        if (str_starts_with($libelle, 'REFUSÉ : ')) {
            $libelle = substr($libelle, strlen('REFUSÉ : '));
        } else {
            $libelle = 'REFUSÉ : ' . $libelle;
        }

        $lfhf->setLibelle($libelle);

        $entityManager->flush();

        return $this->redirectToRoute('app_comptable_info_fiche', [
            'id' => $lfhf->getFicheFrais()->getId()
        ]);
    }
}
