<?php

namespace spec\Controller\Authenticate;

use Dto\Inbound\User\AuthenticatingUser;
use Dto\Outbound\Success;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Trailmind\AuthenticationService\PasswordVerifier;
use Trailmind\User\User;

class AuthenticateControllerSpec extends ObjectBehavior
{
    function let(
        PasswordVerifier $passwordVerifier,
    ) {
        $this->beConstructedWith(
            $passwordVerifier,
        );
    }

    function it_should_verify_a_user_with_password(
        User $user,
        PasswordVerifier $passwordVerifier,
    ) {
        $authenticatingUser = new AuthenticatingUser('email', 'password');

        $passwordVerifier->verifyPassword($user, 'password')->willReturn(true);

        $this->postAuthenticateAction($authenticatingUser, $user)->shouldBeAnInstanceOf(Success::class);
    }

    function it_should_throw_unauthorized_exception_for_invalid_password(
        User $user,
        PasswordVerifier $passwordVerifier,
    ) {
        $authenticatingUser = new AuthenticatingUser('email', 'wrong-password');

        $passwordVerifier->verifyPassword($user, 'wrong-password')->willReturn(false);

        $this->shouldThrow(UnauthorizedHttpException::class)->duringPostAuthenticateAction($authenticatingUser, $user);
    }
}