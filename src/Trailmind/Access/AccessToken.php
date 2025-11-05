<?php

namespace Trailmind\Access;

use DateTime;
use DateTimeImmutable;
use Trailmind\User\User;

class AccessToken
{
    /**
     * @var bool
     */
    private $revoked = false;

    /**
     * @param string   $id
     * @param User     $user
     * @param Client   $client
     * @param array    $scopes
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @param DateTimeImmutable $expiresAt
     */
    public function __construct(
        private string $id,
        private User $user,
        private Client $client,
        private array $scopes,
        private DateTime $createdAt,
        private DateTime $updatedAt,
        private DateTimeImmutable $expiresAt
    ) {}

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
