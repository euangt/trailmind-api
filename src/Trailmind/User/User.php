<?php

namespace Trailmind\User;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private ?string $id = null;
    private ?string $password = null;

    public function __construct(
        private string $email,
        private string $name,
        private array $roles = []
    ) {}

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param string $role
     */
    public function addRole(string $role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    /**
     * @param string $roleToCheck
     *
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
}