<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        $togglToken = $user ? $user->getActiveTogglToken() : null;
        $hasApiToken = $togglToken !== null;

        return $this->render('dashboard/index.html.twig', [
            'has_api_token' => $hasApiToken,
        ]);
    }
}
