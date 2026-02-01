<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TogglToken::class, orphanRemoval: true)]
    private Collection $togglTokens;

    public function __construct()
    {
        $this->togglTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @return Collection<int, TogglToken>
     */
    public function getTogglTokens(): Collection
    {
        return $this->togglTokens;
    }

    public function addTogglToken(TogglToken $togglToken): static
    {
        if (!$this->togglTokens->contains($togglToken)) {
            $this->togglTokens->add($togglToken);
            $togglToken->setUser($this);
        }

        return $this;
    }

    public function removeTogglToken(TogglToken $togglToken): static
    {
        if ($this->togglTokens->removeElement($togglToken)) {
            if ($togglToken->getUser() === $this) {
                $togglToken->setUser(null);
            }
        }

        return $this;
    }

    public function getActiveTogglToken(): ?TogglToken
    {
        // Return the most recent token
        $tokens = $this->togglTokens->toArray();
        if (empty($tokens)) {
            return null;
        }
        
        usort($tokens, function($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });
        
        return $tokens[0];
    }
}
