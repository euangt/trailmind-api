<?php

namespace spec\Infrastructure\Oauth2Server;

use Dto\Internal\Authentication\AccessToken;
use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;
use Trailmind\User\User;

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
        User $user,
        AccessToken $accessToken
    ) {
        $request->server = new ServerBag([
            'HTTP_CLIENT_ID' => 'client-id',
            'HTTP_CLIENT_SECRET' => 'client-secret'
        ]);

        $user->getEmail()->willReturn('user@example.com');


        $inputParams = [
            "grant_type"=>"password",
            "client_id"=>"client-id",
            "client_secret"=>"client-secret",
            "scope"=>"*",
            "username"=>"user@example.com",
            "password"=>""
        ];

        $tokenGranter->grantAccessToken($request, $inputParams)->willReturn($accessToken);

        $this->getAccessToken($request, $user)->shouldReturn($accessToken);
    }
}