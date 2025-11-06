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

    public function __construct(
        private string $id,
        private User $user,
        private Client $client,
        private array $scopes,
        private DateTime $createdAt,
        private DateTime $updatedAt,
        private DateTimeImmutable $expiresAt
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
