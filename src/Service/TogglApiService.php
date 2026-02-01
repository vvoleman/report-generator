<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TogglApiService
{
    private const API_BASE_URL = 'https://api.track.toggl.com/api/v9';
    private const CACHE_TTL = 1800; // 30 minutes

    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        private ParameterBagInterface $params
    ) {
    }

    public function getTimeEntries(string $apiToken, \DateTime $startDate, \DateTime $endDate): array
    {
        $cacheKey = $this->getCacheKey('time_entries', $apiToken, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        return $this->getCachedOrFetch($cacheKey, function() use ($apiToken, $startDate, $endDate) {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/me/time_entries', [
                'auth_basic' => [$apiToken, 'api_token'],
                'query' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
            ]);

            return $response->toArray();
        });
    }

    public function getProjects(string $apiToken, int $workspaceId): array
    {
        $cacheKey = $this->getCacheKey('projects', $apiToken, ['workspace_id' => $workspaceId]);

        return $this->getCachedOrFetch($cacheKey, function() use ($apiToken, $workspaceId) {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . "/workspaces/{$workspaceId}/projects", [
                'auth_basic' => [$apiToken, 'api_token'],
            ]);

            return $response->toArray();
        });
    }

    public function getWorkspaces(string $apiToken): array
    {
        $cacheKey = $this->getCacheKey('workspaces', $apiToken, []);

        return $this->getCachedOrFetch($cacheKey, function() use ($apiToken) {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/me/workspaces', [
                'auth_basic' => [$apiToken, 'api_token'],
            ]);

            return $response->toArray();
        });
    }

    public function getCurrentUser(string $apiToken): array
    {
        $cacheKey = $this->getCacheKey('user', $apiToken, []);

        return $this->getCachedOrFetch($cacheKey, function() use ($apiToken) {
            $response = $this->httpClient->request('GET', self::API_BASE_URL . '/me', [
                'auth_basic' => [$apiToken, 'api_token'],
            ]);

            return $response->toArray();
        });
    }

    private function getCacheKey(string $endpoint, string $apiToken, array $params): string
    {
        $tokenHash = substr(md5($apiToken), 0, 8);
        $paramsHash = md5(json_encode($params));
        return "toggl_{$endpoint}_{$tokenHash}_{$paramsHash}";
    }

    private function getCachedOrFetch(string $cacheKey, callable $fetcher): array
    {
        $cacheEnabled = $this->params->get('app.toggl_cache_enabled') ?? false;

        if (!$cacheEnabled) {
            return $fetcher();
        }

        return $this->cache->get($cacheKey, function(ItemInterface $item) use ($fetcher) {
            $item->expiresAfter(self::CACHE_TTL);
            return $fetcher();
        });
    }
}
