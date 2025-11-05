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

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
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
     * @return DateTimeImmutable
     */
    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
