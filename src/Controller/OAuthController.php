<?php

namespace App\Controller;

use App\Entity\TogglToken;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    #[Route('/oauth/connect/toggl', name: 'oauth_toggl_connect')]
    public function connectToggl(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('toggl')
            ->redirect([], []);
    }

    #[Route('/oauth/check/toggl', name: 'oauth_toggl_check')]
    public function checkToggl(
        Request $request,
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager
    ): Response {
        $client = $clientRegistry->getClient('toggl');

        try {
            $accessToken = $client->getAccessToken();

            $togglToken = new TogglToken();
            $togglToken->setUser($this->getUser());
            $togglToken->setAccessToken($accessToken->getToken());
            $togglToken->setRefreshToken($accessToken->getRefreshToken());
            
            $expiresAt = new \DateTime();
            if ($accessToken->getExpires()) {
                $expiresAt->setTimestamp($accessToken->getExpires());
            } else {
                $expiresAt->modify('+1 year');
            }
            $togglToken->setExpiresAt($expiresAt);

            $entityManager->persist($togglToken);
            $entityManager->flush();

            $this->addFlash('success', 'Toggl account connected successfully!');
        } catch (IdentityProviderException $e) {
            $this->addFlash('error', 'Could not connect to Toggl: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_dashboard');
    }
}
