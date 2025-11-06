<?php

namespace Infrastructure\Oauth2Server\Bridge;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class Scope implements ScopeEntityInterface
{
    use EntityTrait;

    public static $scopes = [];

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setIdentifier($name);
    }

    /**
     * @param string $id
     */
    public static function hasScope($id): bool
    {
        return $id === '*' || array_key_exists($id, static::$scopes);
    }

    /**
     * Get the data that should be serialized to JSON.
     */
    public function jsonSerialize(): mixed
    {
        return $this->getIdentifier();
    }
}
