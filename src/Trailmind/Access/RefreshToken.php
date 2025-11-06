<?php

namespace Trailmind\Access;

use DateTimeImmutable;

class RefreshToken
{
    /**
     * @var bool
     */
    private $revoked = false;

    public function __construct(
        private string $id,
        private AccessToken $accessToken,
        private DateTimeImmutable $expiresAt
    ) {}

    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
