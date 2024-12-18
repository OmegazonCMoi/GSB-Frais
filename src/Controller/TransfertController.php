<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use App\Entity\Etat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TransfertController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/transfer/users', name: 'transfer_users')]
    public function importUsers(): \Symfony\Component\HttpFoundation\Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';

        $data = file_get_contents($filePath);
        $usersData = json_decode($data, true);

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setOldId($userData['id']);
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setEmail(strtolower($userData['nom'] . '.' . $userData['prenom'] . '@gsb.fr'));
            $user->setLogin($userData['login']);
            $user->setAdresse($userData['adresse']);
            $user->setCp($userData['cp']);
            $user->setVille($userData['ville']);
            $user->setDateEmbauche(new \DateTime($userData['dateEmbauche']));
            $currentRoles = $user->getRoles();
            if (!in_array('ROLE_VISITEUR', $currentRoles)) {
                $currentRoles[] = 'ROLE_VISITEUR';
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['mdp']);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        //Render page transfer_users
        return $this->render('transfert/transfert_users.html.twig', [
            'tranfertcontroller' => 'TransfertController',
        ]);

    }
    #[Route('/transfer/fichefrais', name: 'tranfert_fichefrais')]
    public function importFicheFrais(): \Symfony\Component\HttpFoundation\Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/fichefrais.json';

        if (!file_exists($filePath)) {
            return new JsonResponse(['status' => 'error', 'message' => 'File not found'], 404);
        }

        $data = file_get_contents($filePath);
        $fichesData = json_decode($data, true);

        if ($fichesData === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON format'], 400);
        }

        foreach ($fichesData as $ficheData) {
            $fiche = new FicheFrais();
            $mois = \DateTime::createFromFormat('Ym', substr($ficheData['mois'], 0, 6));
            $mois->setDate($mois->format('Y'), $mois->format('m'), 1);
            $fiche->setMois($mois);
            $fiche->setNbJustificatifs($ficheData['nbJustificatifs']);
            $fiche->setMontantValid($ficheData['montantValide']);
            $fiche->setDateModif(new \DateTime($ficheData['dateModif']));
            $fiche->setUser($this->entityManager->getRepository(User::class)->findOneBy(['old_id' => $ficheData['idVisiteur']]));
            switch ($ficheData['idEtat']) {
                case 'CR':
                    $etat = $this->entityManager->getRepository(Etat::class)->find(2);
                    break;
                case 'VA':
                    $etat = $this->entityManager->getRepository(Etat::class)->find(4);
                    break;
                case 'RB':
                    $etat = $this->entityManager->getRepository(Etat::class)->find(3);
                    break;
                case 'CL':
                    $etat = $this->entityManager->getRepository(Etat::class)->find(1);
                    break;
                default:
                    $etat = null;
            }
            $fiche->setEtat($etat);
            $this->entityManager->persist($fiche);
        }

        $this->entityManager->flush();

        //Render page transfer_users
        return $this->render('transfert/transfert_users.html.twig', [
            'tranfertcontroller' => 'TransfertController',
        ]);
    }

    #[Route('/transfer/ligneFraisForfait', name: 'transfer_lignefraisforfait')]
    public function importLigneFraisForfait(): \Symfony\Component\HttpFoundation\Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/lignefraisforfait.json';

        if (!file_exists($filePath)) {
            return new JsonResponse(['status' => 'error', 'message' => 'File not found'], 404);
        }

        $data = file_get_contents($filePath);
        $lignesData = json_decode($data, true);

        if ($lignesData === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON format'], 400);
        }

        foreach ($lignesData as $ligneData) {
            $ligne = new LigneFraisForfait();
            $ligne->setQuantite($ligneData['quantite']);
            $mois = \DateTime::createFromFormat('Ym', substr($ligneData['mois'], 0, 6));
            $mois->setDate($mois->format('Y'), $mois->format('m'), 1);
            $ligne->setFicheFrais($this->entityManager->getRepository(FicheFrais::class)->findOneBy([
                'mois' => $mois,
                'user' => $this->entityManager->getRepository(User::class)->findOneBy(['old_id' => $ligneData['idVisiteur']])]));
            switch ($ligneData['idFraisForfait']) {
                case 'ETP':
                    $fraisForfait = $this->entityManager->getRepository(FraisForfait::class)->find(1);
                    break;
                case 'KM':
                    $fraisForfait = $this->entityManager->getRepository(FraisForfait::class)->find(2);
                    break;
                case 'NUI':
                    $fraisForfait = $this->entityManager->getRepository(FraisForfait::class)->find(3);
                    break;
                case 'REP':
                    $fraisForfait = $this->entityManager->getRepository(FraisForfait::class)->find(4);
                    break;
                default:
                    $fraisForfait = null;
            }
            $ligne->setFraisforfait($fraisForfait);
            $this->entityManager->persist($ligne);
        }

        $this->entityManager->flush();

        //Render page transfer_users
        return $this->render('transfert/transfert_users.html.twig', [
            'tranfertcontroller' => 'TransfertController',
        ]);
    }

    #[Route('/transfer/lignehorsforfait', name: 'transfer_lignehorsforfait')]
    public function importLigneHorsForfait(): \Symfony\Component\HttpFoundation\Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/lignefraishorsforfait.json';

        if (!file_exists($filePath)) {
            return new JsonResponse(['status' => 'error', 'message' => 'File not found'], 404);
        }

        $data = file_get_contents($filePath);
        $lignesData = json_decode($data, true);

        if ($lignesData === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON format'], 400);
        }

        foreach ($lignesData as $ligneData) {
            $ligne = new LigneFraisHorsForfait();
            $ligne->setLibelle($ligneData['libelle']);
            $ligne->setMontant($ligneData['montant']);
            $ligne->setDate(new \DateTime($ligneData['date']));
            $mois = \DateTime::createFromFormat('Ym', substr($ligneData['mois'], 0, 6));
            $mois->setDate($mois->format('Y'), $mois->format('m'), 1);
            $ligne->setFicheFrais($this->entityManager->getRepository(FicheFrais::class)->findOneBy([
                'mois' => $mois,
                'user' => $this->entityManager->getRepository(User::class)->findOneBy(['old_id' => $ligneData['idVisiteur']])]));

            $this->entityManager->persist($ligne);
        }

        $this->entityManager->flush();

        //Render page transfer_users
        return $this->render('transfert/transfert_users.html.twig', [
            'tranfertcontroller' => 'TransfertController',
        ]);
    }
}