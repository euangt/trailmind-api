<?php

namespace spec\Infrastructure\Security\Authenticator;

use Infrastructure\Oauth2Server\TokenManager;
use Infrastructure\Security\Authenticator\BearerTokenAuthenticator;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;
use Trailmind\User\User;

class BearerTokenAuthenticatorSpec extends ObjectBehavior
{
    function let(TokenManager $tokenManager)
    {
        $this->beConstructedWith($tokenManager);
    }

    function it_should_support_requests_with_an_authorization_header()
    {
        $request = Request::create('/v1.0/trails', 'GET', server: [
            'HTTP_AUTHORIZATION' => 'Bearer access-token',
        ]);

        $this->supports($request)->shouldReturn(true);
    }

    function it_should_not_support_requests_without_an_authorization_header()
    {
        $request = Request::create('/v1.0/trails', 'GET');

        $this->supports($request)->shouldReturn(false);
    }

    function it_should_authenticate_a_request_with_a_valid_user(
        TokenManager $tokenManager,
        User $user
    ) {
        $request = Request::create('/v1.0/trails', 'GET', server: [
            'HTTP_AUTHORIZATION' => 'Bearer access-token',
        ]);

        $tokenManager->findUser($request)->shouldBeCalled()->willReturn($user);
        $user->getEmail()->willReturn('user@example.com');

        $this->authenticate($request)->shouldHaveType(Passport::class);
    }

    function it_should_throw_if_the_access_token_is_invalid(
        TokenManager $tokenManager
    ) {
        $request = Request::create('/v1.0/trails', 'GET', server: [
            'HTTP_AUTHORIZATION' => 'Bearer access-token',
        ]);

        $tokenManager->findUser($request)->shouldBeCalled()->willThrow(InvalidAccessTokenException::class);

        $this->shouldThrow(CustomUserMessageAuthenticationException::class)->duringAuthenticate($request);
    }

    function it_should_return_null_on_authentication_success(
        Request $request,
        TokenInterface $token
    ) {
        $this->onAuthenticationSuccess($request, $token, 'main')->shouldReturn(null);
    }
}
