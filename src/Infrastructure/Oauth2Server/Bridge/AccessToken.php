<?php

namespace Infrastructure\Oauth2Server\Bridge;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait; use EntityTrait; use TokenEntityTrait;

    public function __construct(string $userIdentifier, array $scopes = [])
    {
        $this->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }
}
