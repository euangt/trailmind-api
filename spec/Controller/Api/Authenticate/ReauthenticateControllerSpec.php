<?php

namespace spec\Controller\Api\Authenticate;

use Application\Authentication\AccessToken;
use Application\Dto\Inbound\User\ReauthenticatingUser;
use Dto\Outbound\Authentication\AccessTokenBuilder;
use Dto\Outbound\Success;
use Infrastructure\Oauth2Server\TokenManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Trailmind\AuthenticationService\Exception\InvalidRefreshTokenException;
use Trailmind\AuthenticationService\Exception\TokenRequestException;
use Trailmind\AuthenticationService\Exception\UnableToCreateAccessTokenException;

class ReauthenticateControllerSpec extends ObjectBehavior
{
    function let(
        TokenManager $tokenManager,
        AccessTokenBuilder $accessTokenBuilder
    ) {
        $this->beConstructedWith(
            $tokenManager,
            $accessTokenBuilder
        );
    }

    function it_should_refresh_an_access_token(
        TokenManager $tokenManager,
        AccessToken $accessToken,
        Request $request,
        Success $success,
        AccessTokenBuilder $accessTokenBuilder
    ) {
        $reauthenticatingUser = new ReauthenticatingUser('refresh-token');

        $tokenManager->reauthenticate($request, $reauthenticatingUser)->shouldBeCalled()->willReturn($accessToken);

        $accessTokenBuilder->setContext('v1.0_reauthenticate')->shouldBeCalled()->willReturn($accessTokenBuilder);
        $accessTokenBuilder->build($accessToken)->shouldBeCalled()->willReturn($success);

        $this->postReauthenticateAction($reauthenticatingUser, $request)->shouldReturn($success);
    }

    function it_should_return_a_json_error_for_invalid_refresh_token(
        TokenManager $tokenManager,
        Request $request
    ) {
        $reauthenticatingUser = new ReauthenticatingUser('invalid-refresh-token');

        $tokenManager->reauthenticate($request, $reauthenticatingUser)->shouldBeCalled()->willThrow(InvalidRefreshTokenException::class);

        $response = $this->postReauthenticateAction($reauthenticatingUser, $request);

        $response->shouldBeAnInstanceOf(JsonResponse::class);
        $response->getStatusCode()->shouldReturn(401);
    }

    function it_should_return_a_json_error_for_generic_token_request_failures(
        TokenManager $tokenManager,
        Request $request
    ) {
        $reauthenticatingUser = new ReauthenticatingUser('refresh-token');

        $tokenManager->reauthenticate($request, $reauthenticatingUser)->shouldBeCalled()->willThrow(
            new TokenRequestException([
                'error' => 'invalid_client',
                'error_description' => 'Client authentication failed',
            ], 401)
        );

        $response = $this->postReauthenticateAction($reauthenticatingUser, $request);

        $response->shouldBeAnInstanceOf(JsonResponse::class);
        $response->getStatusCode()->shouldReturn(401);
    }

    function it_should_return_a_server_error_json_response_when_unable_to_refresh_access_token(
        TokenManager $tokenManager,
        Request $request
    ) {
        $reauthenticatingUser = new ReauthenticatingUser('refresh-token');

        $tokenManager->reauthenticate($request, $reauthenticatingUser)->shouldBeCalled()->willThrow(UnableToCreateAccessTokenException::class);

        $response = $this->postReauthenticateAction($reauthenticatingUser, $request);

        $response->shouldBeAnInstanceOf(JsonResponse::class);
        $response->getStatusCode()->shouldReturn(500);
    }
}
