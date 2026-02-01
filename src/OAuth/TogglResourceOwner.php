<?php

namespace App\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class TogglResourceOwner implements ResourceOwnerInterface
{
    protected array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId(): int
    {
        return $this->response['id'];
    }

    public function getEmail(): string
    {
        return $this->response['email'] ?? '';
    }

    public function getFullName(): string
    {
        return $this->response['fullname'] ?? '';
    }

    public function toArray(): array
    {
        return $this->response;
    }
}
