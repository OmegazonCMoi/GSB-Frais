<?php

namespace App\Controller;

use App\Repository\FicheFraisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TopVisiteursController extends AbstractController
{
    #[Route('/top-visiteurs', name: 'top_visiteurs')]
    public function topVisiteursAction(Request $request, FicheFraisRepository $ficheFraisRepository)
    {
        $mois = $request->query->get('mois', date('Y-m'));

        $moisDate = \DateTime::createFromFormat('Y-m-d', $mois . '-01');

        $fichesFrais = $ficheFraisRepository->findBy(['mois' => $moisDate]);

        $visiteursFrais = [];
        foreach ($fichesFrais as $fiche) {
            $userId = $fiche->getUser()->getId();
            if (!isset($visiteursFrais[$userId])) {
                $visiteursFrais[$userId] = [
                    'nom' => $fiche->getUser()->getNom(),
                    'prenom' => $fiche->getUser()->getPrenom(),
                    'totalFrais' => 0
                ];
            }
            $visiteursFrais[$userId]['totalFrais'] += $fiche->getMontantValid();
        }

        uasort($visiteursFrais, function ($a, $b) {
            return $b['totalFrais'] <=> $a['totalFrais'];
        });

        $topVisiteurs = array_slice($visiteursFrais, 0, 3);

        return $this->render('top/top_visiteurs.html.twig', [
            'topVisiteurs' => $topVisiteurs,
            'mois' => $mois,
        ]);
    }
}
