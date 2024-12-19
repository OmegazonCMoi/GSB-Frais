<?php

namespace App\Controller;

use App\Repository\FicheFraisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TopVisiteursController extends AbstractController
{
    /**
     * @Route("/top-visiteurs", name="top_visiteurs")
     */
    #[Route('/top-visiteurs', name: 'top_visiteurs')]
    public function topVisiteursAction(Request $request, FicheFraisRepository $ficheFraisRepository)
    {
        // Récupérer le mois depuis la requête, par défaut mois actuel
        $mois = $request->query->get('mois', date('Y-m'));

        // Convertir le mois au format 'YYYY-MM-01' (premier jour du mois)
        $moisDate = \DateTime::createFromFormat('Y-m-d', $mois . '-01');  // Ajout du jour et création d'un objet DateTime

        // Récupération de toutes les fiches pour le mois sélectionné
        $fichesFrais = $ficheFraisRepository->findBy(['mois' => $moisDate]);

        // Regrouper par utilisateur et calculer le total des frais validés
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

        // Trier les visiteurs par totalFrais décroissant et récupérer les 3 premiers
        uasort($visiteursFrais, function ($a, $b) {
            return $b['totalFrais'] <=> $a['totalFrais'];
        });

        $topVisiteurs = array_slice($visiteursFrais, 0, 3);

        // Passer les données à la vue
        return $this->render('top/top_visiteurs.html.twig', [
            'topVisiteurs' => $topVisiteurs,
            'mois' => $mois,
        ]);
    }
}
