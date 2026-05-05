<?php

namespace Trailmind\AuthenticationService\Exception;

use League\OAuth2\Server\Exception\OAuthServerException;

class TokenRequestException extends UnableToCreateAccessTokenException
{
    /**
     * @param array<string, string> $payload
     */
    public function __construct(
        private array $payload,
        private int $statusCode,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($payload['error_description'] ?? 'Unable to complete token request', 0, $previous);
    }

    public static function fromOAuthException(OAuthServerException $exception): self
    {
        return new self($exception->getPayload(), $exception->getHttpStatusCode(), $exception);
    }

    /**
     * @return array<string, string>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
