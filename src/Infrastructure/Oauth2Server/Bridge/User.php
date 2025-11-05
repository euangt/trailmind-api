<?php

namespace Infrastructure\Oauth2Server\Bridge;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

class User implements UserEntityInterface
{
    use EntityTrait;

    /**
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        $this->setIdentifier($identifier);
    }
}
