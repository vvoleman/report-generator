<?php

namespace App\Controller;

use App\Entity\TogglToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(): Response
    {
        $user = $this->getUser();
        $togglToken = $user->getActiveTogglToken();

        return $this->render('settings/index.html.twig', [
            'current_token' => $togglToken ? $togglToken->getApiToken() : null,
        ]);
    }

    #[Route('/settings/toggl-token', name: 'app_settings_save_token', methods: ['POST'])]
    public function saveTogglToken(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Verify CSRF token
        $submittedToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('save_toggl_token', $submittedToken)) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_settings');
        }

        $apiToken = trim($request->request->get('api_token', ''));

        if (empty($apiToken)) {
            $this->addFlash('error', 'API token cannot be empty.');
            return $this->redirectToRoute('app_settings');
        }

        $user = $this->getUser();
        
        // Get existing token or create new one
        $togglToken = $user->getActiveTogglToken();
        if (!$togglToken) {
            $togglToken = new TogglToken();
            $togglToken->setUser($user);
        }
        
        $togglToken->setApiToken($apiToken);

        $entityManager->persist($togglToken);
        $entityManager->flush();

        $this->addFlash('success', 'Toggl API token saved successfully!');
        
        return $this->redirectToRoute('app_dashboard');
    }
}
