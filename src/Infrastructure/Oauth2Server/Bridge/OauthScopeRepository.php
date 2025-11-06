<?php

namespace Infrastructure\Oauth2Server\Bridge;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class OauthScopeRepository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        if (Scope::hasScope($identifier)) {
            return new Scope($identifier);
        }
        return null;
    }

    public function finalizeScopes(
        array $scopes,
        string $grantType,
        ClientEntityInterface $clientEntity,
        string|null $userIdentifier = null,
        ?string $authCodeId = null
    ): array {
        $filteredScopes = [];
        foreach ($scopes as $scope) {
            if (Scope::hasScope($scope->getIdentifier())) {
                $filteredScopes[] = $scope;
            }
        }
        return $filteredScopes;
    }
}
