<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        $secret = $user->getGoogleAuthenticatorSecret();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'secret' => $secret,
        ]);
    }
}
