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

    /**
     * @param string $id
     * @param string $name
     */
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getRedirect(): ?string
    {
        return $this->redirect;
    }

    /**
     * @param string $redirect
     */
    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    /**
     * @return bool
     */
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

    /**
     * @param bool $requiresVerification
     */
    public function setRequiresVerification(bool $requiresVerification)
    {
        $this->requiresVerification = $requiresVerification;
    }

    public function addRole(string $role)
    {
        $this->roles[] = $role;
    }

    /**
     * @inheritdoc
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->secret;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials(): void
    {
        // Nothing
    }

    /**
     * @inheritdoc
     */
    public function getUserIdentifier(): string
    {
        return $this->id;
    }
}
