<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TogglApiService
{
    private const API_BASE_URL = 'https://api.track.toggl.com/api/v9';

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function getTimeEntries(string $apiToken, \DateTime $startDate, \DateTime $endDate): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . '/me/time_entries', [
            'auth_basic' => [$apiToken, 'api_token'],
            'query' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
        ]);

        return $response->toArray();
    }

    public function getProjects(string $apiToken, int $workspaceId): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . "/workspaces/{$workspaceId}/projects", [
            'auth_basic' => [$apiToken, 'api_token'],
        ]);

        return $response->toArray();
    }

    public function getWorkspaces(string $apiToken): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . '/me/workspaces', [
            'auth_basic' => [$apiToken, 'api_token'],
        ]);

        return $response->toArray();
    }

    public function getCurrentUser(string $apiToken): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . '/me', [
            'auth_basic' => [$apiToken, 'api_token'],
        ]);

        return $response->toArray();
    }
}
