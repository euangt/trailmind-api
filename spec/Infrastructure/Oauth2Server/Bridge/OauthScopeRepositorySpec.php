<?php

namespace spec\Infrastructure\Oauth2Server\Bridge;

use PhpSpec\ObjectBehavior;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Infrastructure\Oauth2Server\Bridge\Scope;

class OauthScopeRepositorySpec extends ObjectBehavior
{
    
    function it_should_be_an_ScopeRepositoryInterface()
    {
        $this->shouldBeAnInstanceOf(ScopeRepositoryInterface::class);
    }

    function it_should_get_a_supported_scope_by_identifier()
    {
        $this->getScopeEntityByIdentifier('*')->shouldBeAnInstanceOf(Scope::class);
    }
    
    function it_should_not_get_an_unsupported_scope_by_identifier()
    {
        $this->getScopeEntityByIdentifier('unsupported')->shouldReturn(null);
    }
    
    function it_should_finalise_scopes_from_a_shortlist(
        Scope $scope1,
        Scope $scope2,
        ClientEntityInterface $clientEntity
    ) {
        $scope1->getIdentifier()->willReturn("unsupported");
        $scope2->getIdentifier()->willReturn("*");
        
        $scopes = $this->finalizeScopes(
            [$scope1, $scope2],
            "password",
            $clientEntity,
            "1234"
        );
        
        $scopes->shouldBe([$scope2]);
    }
}