<?php

namespace Trailmind\Access;

use Symfony\Component\Security\Core\User\UserInterface;

class Client implements UserInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $redirect;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var boolean
     */
    private $requiresVerification;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var SalesChannel|null
     */
    private $salesChannel;

    public function __construct(
        private string $id,
        private string $name
    ) {
        $this->active = true;
        $this->requiresVerification = true;
        $this->roles = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    public function getRedirect(): ?string
    {
        return $this->redirect;
    }

    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    /**
     * @return boolean
     */
    public function requiresVerification()
    {
        return $this->requiresVerification;
    }

    public function setRequiresVerification(bool $requiresVerification)
    {
        $this->requiresVerification = $requiresVerification;
    }

    public function addRole(string $role)
    {
        $this->roles[] = $role;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->secret;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->name;
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
