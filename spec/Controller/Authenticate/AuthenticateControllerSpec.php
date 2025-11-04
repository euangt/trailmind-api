<?php

namespace spec\Controller\Authenticate;

use Dto\Inbound\User\AuthenticatingUser;
use Dto\Internal\Authentication\AccessToken;
use Dto\Outbound\Success;
use Infrastructure\Oauth2Server\TokenManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Trailmind\AuthenticationService\Exception\UnableToCreateAccessTokenException;
use Trailmind\AuthenticationService\PasswordVerifier;
use Trailmind\User\User;

class AuthenticateControllerSpec extends ObjectBehavior
{
    function let(
        PasswordVerifier $passwordVerifier,
        TokenManager $tokenManager
    ) {
        $this->beConstructedWith(
            $passwordVerifier,
            $tokenManager
        );
    }

    function it_should_verify_a_user_with_password(
        User $user,
        PasswordVerifier $passwordVerifier,
        TokenManager $tokenManager,
        AccessToken $accessToken,
        Request $request
    ) {
        $authenticatingUser = new AuthenticatingUser('email', 'password');

        $passwordVerifier->verifyPassword($user, 'password')->willReturn(true);

        $tokenManager->getAccessToken($request, $user)->willReturn($accessToken);

        $this->postAuthenticateAction($authenticatingUser, $user, $request)->shouldBeAnInstanceOf(Success::class);
    }

    function it_should_throw_unauthorized_exception_for_invalid_password(
        User $user,
        PasswordVerifier $passwordVerifier,
        Request $request
    ) {
        $authenticatingUser = new AuthenticatingUser('email', 'wrong-password');

        $passwordVerifier->verifyPassword($user, 'wrong-password')->willReturn(false);

        $this->shouldThrow(UnauthorizedHttpException::class)->duringPostAuthenticateAction($authenticatingUser, $user, $request);
    }

    function it_should_throw_bad_request_exception_when_unable_to_create_access_token(
        User $user,
        PasswordVerifier $passwordVerifier,
        TokenManager $tokenManager,
        Request $request
    ) {
        $authenticatingUser = new AuthenticatingUser('email', 'password');

        $passwordVerifier->verifyPassword($user, 'password')->willReturn(true);

        $tokenManager->getAccessToken($request, $user)->willThrow(UnableToCreateAccessTokenException::class);

        $this->shouldThrow(BadRequestHttpException::class)->duringPostAuthenticateAction($authenticatingUser, $user, $request);
    }
}