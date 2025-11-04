<?php

namespace spec\Infrastructure\Oauth2Server\TokenGranter;

use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use PhpSpec\ObjectBehavior;

class OpaqueTokenGranterSpec extends ObjectBehavior
{
    function let(
        AuthorizationServer $authorizationServer,
        PasswordGrant $passwordGrant,
        RefreshTokenGrant $refreshTokenGrant
    ) {
        $this->beConstructedWith($authorizationServer, $passwordGrant, $refreshTokenGrant);
    }

    function it_should_be_an_instance_of_TokenGranter()
    {
        $this->shouldImplement(TokenGranter::class);
    }
}