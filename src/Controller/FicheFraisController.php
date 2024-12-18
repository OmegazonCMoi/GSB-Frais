<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Form\FicheFraisType;
use App\Form\MonthType;
use App\Form\SaisisFraisForfaitType;
use App\Form\SaisisFraisHorsForfaitType;
use App\Repository\FicheFraisRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fiche/frais')]
final class FicheFraisController extends AbstractController
{



    #[Route(name: 'app_fiche_frais_index', methods: ['GET', 'POST'])]
    public function index(Request $request, FicheFraisRepository $ficheFraisRepository): Response
    {
        $user = $this->getUser();
        $ficheSelectionnee = null; // Initialisation de la fiche sélectionnée

        // Récupération des fiches frais de l'utilisateur
        $fiches = $ficheFraisRepository->findBy(['user' => $user]);

        // Création du formulaire avec les fiches disponibles
        $form = $this->createForm(MonthType::class, null, [
            'fiches' => $fiches,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de la fiche frais sélectionnée dans le formulaire
            /** @var FicheFrais $ficheSelectionnee */
            $ficheSelectionnee = $form->get('mois')->getData();
        }

        // Passage des données à la vue
        return $this->render('fiche_frais/index.html.twig', [
            'form' => $form,
            'fiche_frais' => $ficheSelectionnee, // Fiches frais sélectionnées
        ]);
    }
    #[Route('/new', name: 'app_fiche_frais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ficheFrai = new FicheFrais();
        $form = $this->createForm(FicheFraisType::class, $ficheFrai);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ficheFrai);
            $entityManager->flush();

            return $this->redirectToRoute('app_fiche_frais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fiche_frais/new.html.twig', [
            'fiche_frai' => $ficheFrai,
            'form' => $form,
        ]);
    }

    #[Route('/update', name: 'app_fiche_frais_update', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        $firstDayOfMonth = new DateTime('first day of this month');
        // check if fiche already exists from month and user
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy([
            'mois' => $firstDayOfMonth,
            'user' => $this->getUser(),
        ]);

        // if already exists update it
        if ($ficheFrais == null) {
            $ficheFrais =  new FicheFrais();
            $ficheFrais->setMois($firstDayOfMonth);
            $ficheFrais->setUser($this->getUser());
            $ficheFrais->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['id' => 2]));
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid('0');
            $ficheFrais->setDateModif(new DateTime());

            $ligneFraisForfaitEtape = new LigneFraisForfait();
            $ligneFraisForfaitEtape->setFicheFrais($ficheFrais);
            $ligneFraisForfaitEtape->setFraisforfait($entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 1]));
            $ligneFraisForfaitEtape->setQuantite(0);

            $ligneFraisForfaitKm = new LigneFraisForfait();
            $ligneFraisForfaitKm->setFicheFrais($ficheFrais);
            $ligneFraisForfaitKm->setFraisforfait($entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 2]));
            $ligneFraisForfaitKm->setQuantite(0);

            $ligneFraisForfaitNuitee = new LigneFraisForfait();
            $ligneFraisForfaitNuitee->setFicheFrais($ficheFrais);
            $ligneFraisForfaitNuitee->setFraisforfait($entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 3]));
            $ligneFraisForfaitNuitee->setQuantite(0);

            $ligneFraisForfaitRepas = new LigneFraisForfait();
            $ligneFraisForfaitRepas->setFicheFrais($ficheFrais);
            $ligneFraisForfaitRepas->setFraisforfait($entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 4]));
            $ligneFraisForfaitRepas->setQuantite(0);

            $entityManager->persist($ficheFrais);
            $entityManager->persist($ligneFraisForfaitEtape);
            $entityManager->persist($ligneFraisForfaitKm);
            $entityManager->persist($ligneFraisForfaitNuitee);
            $entityManager->persist($ligneFraisForfaitRepas);
            $entityManager->flush();
        }

        $formFraisForfait = $this->createForm(SaisisFraisForfaitType::class, [
            'km' => $ficheFrais->getLigneFraisForfaits()[1]->getQuantite(),
            'nuitees' => $ficheFrais->getLigneFraisForfaits()[2]->getQuantite(),
            'repas' => $ficheFrais->getLigneFraisForfaits()[3]->getQuantite(),
            'etape' => $ficheFrais->getLigneFraisForfaits()[0]->getQuantite(),
        ]);
        $formFraisHorsForfait = $this->createForm(SaisisFraisHorsForfaitType::class);
        $formFraisForfait->handleRequest($request);
        $formFraisHorsForfait->handleRequest($request);

        if ($formFraisForfait->isSubmitted() && $formFraisForfait->isValid()) {
            $ligneFraisForfaitEtape = $entityManager->getRepository(LigneFraisForfait::class)->findOneBy([
                'ficheFrais' => $ficheFrais,
                'fraisforfait' => $entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 1]),
            ]);
            $ligneFraisForfaitEtape->setQuantite($formFraisForfait->get('etape')->getData());

            $ligneFraisForfaitKm = $entityManager->getRepository(LigneFraisForfait::class)->findOneBy([
                'ficheFrais' => $ficheFrais,
                'fraisforfait' => $entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 2]),
            ]);
            $ligneFraisForfaitKm->setQuantite($formFraisForfait->get('km')->getData());

            $ligneFraisForfaitNuitee = $entityManager->getRepository(LigneFraisForfait::class)->findOneBy([
                'ficheFrais' => $ficheFrais,
                'fraisforfait' => $entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 3]),
            ]);
            $ligneFraisForfaitNuitee->setQuantite($formFraisForfait->get('nuitees')->getData());

            $ligneFraisForfaitRepas = $entityManager->getRepository(LigneFraisForfait::class)->findOneBy([
                'ficheFrais' => $ficheFrais,
                'fraisforfait' => $entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => 4]),
            ]);
            $ligneFraisForfaitRepas->setQuantite($formFraisForfait->get('repas')->getData());

            $entityManager->flush();

            return $this->redirectToRoute('app_fiche_frais_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($formFraisHorsForfait->isSubmitted() && $formFraisHorsForfait->isValid()) {
            $ligneFraisHorsForfait = new LigneFraisHorsForfait();
            $ligneFraisHorsForfait->setFicheFrais($ficheFrais);
            $ligneFraisHorsForfait->setLibelle($formFraisHorsForfait->get('motif')->getData());
            $ligneFraisHorsForfait->setDate($formFraisHorsForfait->get('date')->getData());
            $ligneFraisHorsForfait->setMontant($formFraisHorsForfait->get('montant')->getData());

            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_fiche_frais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fiche_frais/update.html.twig', [
            'formFraisForfait' => $formFraisForfait,
            'formFraisHorsForfait' => $formFraisHorsForfait,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_fiche_frais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FicheFrais $ficheFrai, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FicheFraisType::class, $ficheFrai);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fiche_frais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fiche_frais/edit.html.twig', [
            'fiche_frai' => $ficheFrai,
            'form' => $form,
        ]);
    }

}
