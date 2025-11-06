<?php

namespace Trailmind\User;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private ?string $id = null;

    private ?string $password = null;

    private ?string $token = null;

    public function __construct(
        private string $email,
        private string $name,
        private string $username,
        private array $roles = []
    ) {}

    public function getId(): ?string {
        return $this->id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(string $role)
    {
        if (! $this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    /**
     * @return bool
     */
    public function hasRole(string $roleToCheck)
    {
        foreach ($this->roles as $role) {
            if ($role === $roleToCheck) {
                return true;
            }
        }
        return false;
    }

    public function eraseCredentials(): void
    {
        // Nothing
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }
}