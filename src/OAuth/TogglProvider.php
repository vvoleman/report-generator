<?php

namespace App\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class TogglProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected string $urlAuthorize = 'https://api.track.toggl.com/oauth/authorize';
    protected string $urlAccessToken = 'https://api.track.toggl.com/oauth/token';
    protected string $urlResourceOwnerDetails = 'https://api.track.toggl.com/api/v9/me';

    public function getBaseAuthorizationUrl(): string
    {
        return $this->urlAuthorize;
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->urlAccessToken;
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->urlResourceOwnerDetails;
    }

    protected function getDefaultScopes(): array
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['error'] ?? $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): TogglResourceOwner
    {
        return new TogglResourceOwner($response);
    }
}
