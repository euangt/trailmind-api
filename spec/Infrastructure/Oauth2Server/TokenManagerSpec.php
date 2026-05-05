<?php

namespace spec\Infrastructure\Oauth2Server;

use Application\Authentication\AccessToken;
use Application\Dto\Inbound\User\AuthenticatingUser;
use Application\Dto\Inbound\User\ReauthenticatingUser;
use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;
use Trailmind\User\Exception\UserNotFoundException;
use Trailmind\User\User;
use Trailmind\User\UserRepository;

class TokenManagerSpec extends ObjectBehavior
{
    function let(
        TokenGranter $tokenGranter,
        ResourceServer $resourceServer,
        UserRepository $userRepository
    ) {
        $this->beConstructedWith(
            $tokenGranter,
            $resourceServer,
            $userRepository
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

    function it_should_return_null_if_no_authorization_header_is_present()
    {
        $request = Request::create('/v1.0/trails', 'GET');

        $this->findUser($request)->shouldReturn(null);
    }

    function it_should_find_a_user_from_a_valid_bearer_token(
        ResourceServer $resourceServer,
        UserRepository $userRepository,
        User $user,
        ServerRequestInterface $validatedRequest
    ) {
        $request = Request::create('/v1.0/trails', 'GET', server: [
            'HTTP_AUTHORIZATION' => 'Bearer valid-token',
        ]);

        $resourceServer->validateAuthenticatedRequest(Argument::type(ServerRequestInterface::class))
            ->shouldBeCalled()
            ->willReturn($validatedRequest);
        $validatedRequest->getAttribute('oauth_access_token_id')->willReturn('access-token-id');
        $userRepository->findOneByAccessToken('access-token-id')->shouldBeCalled()->willReturn($user);

        $this->findUser($request)->shouldReturn($user);
    }

    function it_should_throw_if_the_authorization_header_is_not_bearer(
    ) {
        $request = Request::create('/v1.0/trails', 'GET', server: [
            'HTTP_AUTHORIZATION' => 'Basic credentials',
        ]);

        $this->shouldThrow(InvalidAccessTokenException::class)->duringFindUser($request);
    }

    function it_should_throw_if_the_bearer_token_cannot_be_validated(
        ResourceServer $resourceServer
    ) {
        $request = Request::create('/v1.0/trails', 'GET', server: [
            'HTTP_AUTHORIZATION' => 'Bearer invalid-token',
        ]);

        $resourceServer->validateAuthenticatedRequest(Argument::type(ServerRequestInterface::class))
            ->shouldBeCalled()
            ->willThrow(OAuthServerException::accessDenied('Access token could not be verified'));

        $this->shouldThrow(InvalidAccessTokenException::class)->duringFindUser($request);
    }

    function it_should_throw_if_no_user_can_be_found_for_the_token(
        ResourceServer $resourceServer,
        UserRepository $userRepository,
        ServerRequestInterface $validatedRequest
    ) {
        $request = Request::create('/v1.0/trails', 'GET', server: [
            'HTTP_AUTHORIZATION' => 'Bearer valid-token',
        ]);

        $resourceServer->validateAuthenticatedRequest(Argument::type(ServerRequestInterface::class))
            ->shouldBeCalled()
            ->willReturn($validatedRequest);
        $validatedRequest->getAttribute('oauth_access_token_id')->willReturn('missing-access-token');
        $userRepository->findOneByAccessToken('missing-access-token')->shouldBeCalled()->willThrow(UserNotFoundException::class);

        $this->shouldThrow(InvalidAccessTokenException::class)->duringFindUser($request);
    }
}
