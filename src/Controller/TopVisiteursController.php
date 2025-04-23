<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TopVisiteursController extends AbstractController
{
    #[Route('/topvisiteurs', name: 'app_top_visiteurs', methods: ['GET', 'POST'])]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $moisList = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre',
        ];

        $ficheFraisRepository = [];

        if ($request->isMethod('POST')) {
            $selectMonth = '2024-' . $request->request->get('mois') . '-01';
            $ficheFraisRepository = $doctrine->getRepository(FicheFrais::class)->findTopVisiteursMontants($selectMonth);
            usort($ficheFraisRepository, function ($a, $b) {
                return $b->getMontantValid() <=> $a->getMontantValid();
            });
            $ficheFraisRepository = array_slice($ficheFraisRepository, 0, 3);
        }

        return $this->render('top_visiteurs/index.html.twig', [
            'controller_name' => 'TopVisiteursController',
            'moisList' => $moisList,
            'ficheFrais' => $ficheFraisRepository,
        ]);
    }
}