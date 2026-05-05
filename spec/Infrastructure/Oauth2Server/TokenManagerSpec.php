<?php

namespace spec\Infrastructure\Oauth2Server;

use Application\Authentication\AccessToken;
use Application\Dto\Inbound\User\AuthenticatingUser;
use Application\Dto\Inbound\User\ReauthenticatingUser;
use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;

class TokenManagerSpec extends ObjectBehavior
{
    function let(
        TokenGranter $tokenGranter
    ) {
        $this->beConstructedWith(
            $tokenGranter
        );
    }

    function it_should_get_an_access_token(
        TokenGranter $tokenGranter,
        Request $request,
        AccessToken $accessToken
    ) {
        $authenticatingUser = new AuthenticatingUser('user@example.com', 'password');

        $request->server = new ServerBag([
            'HTTP_CLIENT_ID' => 'client-id',
            'HTTP_CLIENT_SECRET' => 'client-secret'
        ]);

        $inputParams = [
            "grant_type"=>"password",
            "client_id"=>"client-id",
            "client_secret"=>"client-secret",
            "scope"=>"email",
            "username"=>"user@example.com",
            "password"=>"password"
        ];

        $tokenGranter->grantAccessToken($request, $inputParams)->shouldBeCalled()->willReturn($accessToken);

        $this->getAccessToken($request, $authenticatingUser)->shouldReturn($accessToken);
    }

    function it_should_reauthenticate_a_user(
        TokenGranter $tokenGranter,
        Request $request,
        AccessToken $accessToken
    ) {
        $reauthenticatingUser = new ReauthenticatingUser('refresh-token');

        $request->server = new ServerBag([
            'HTTP_CLIENT_ID' => 'client-id',
            'HTTP_CLIENT_SECRET' => 'client-secret'
        ]);

        $inputParams = [
            "grant_type"=>"refresh_token",
            "client_id"=>"client-id",
            "client_secret"=>"client-secret",
            "refresh_token"=>"refresh-token"
        ];

        $tokenGranter->refreshAccessToken($request, $inputParams)->shouldBeCalled()->willReturn($accessToken);

        $this->reauthenticate($request, $reauthenticatingUser)->shouldReturn($accessToken);
    }
}
