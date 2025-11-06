<?php

namespace Dto\Outbound\Authentication;

use Dto\Internal\Authentication\AccessToken;
use Dto\Outbound\EntityBuilder;
use Dto\Outbound\Success;

class AccessTokenBuilder extends EntityBuilder
{
    protected const EXPECTED_CLASS = AccessToken::class;

    private AccessToken $accessToken;

    protected function initialise($initialisable): EntityBuilder
    {
        // For readability
        $this->accessToken = $initialisable;

        $this->dto = new Success();
        $this->dto->add('access_token', $this->accessToken->accessToken);
        $this->dto->add('refresh_token', $this->accessToken->refreshToken);
        $this->dto->add('expires_in', $this->accessToken->expiresIn);

        return $this;
    }
}