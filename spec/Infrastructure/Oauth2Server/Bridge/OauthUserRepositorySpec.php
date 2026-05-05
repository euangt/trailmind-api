<?php

namespace spec\Infrastructure\Oauth2Server\Bridge;

use Infrastructure\Oauth2Server\Bridge\User;
use PhpSpec\ObjectBehavior;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Trailmind\AuthenticationService\PasswordVerifier;
use Trailmind\User\Exception\UserNotFoundException;
use Trailmind\User\User as TrailmindUser;
use Trailmind\User\UserRepository;

class OauthUserRepositorySpec extends ObjectBehavior
{
    function let(
        UserRepository $userRepository,
        PasswordVerifier $passwordVerifier
    ) {
        $this->beConstructedWith($userRepository, $passwordVerifier);
    }

    function it_should_be_an_UserRepositoryInterface()
    {
        $this->shouldBeAnInstanceOf(UserRepositoryInterface::class);
    }

    function it_should_get_a_user_by_its_credentials(
        UserRepository $userRepository,
        PasswordVerifier $passwordVerifier,
        TrailmindUser $futureFarmUser,
        ClientEntityInterface $clientEntity
    ) {
        $futureFarmUser->getId()->shouldBeCalled()->willReturn('55');
        $userRepository->findOneByEmail('email')->shouldBeCalled()->willReturn($futureFarmUser);
        $passwordVerifier->verifyPassword($futureFarmUser, 'password')->shouldBeCalled()->willReturn(true);

        $user = $this->getUserEntityByUserCredentials('email', 'password', 'password', $clientEntity);

        $user->shouldBeAnInstanceOf(User::class);
    }

    function it_should_not_get_a_user_by_its_credentials_if_email_not_valid(
        UserRepository $userRepository,
        ClientEntityInterface $clientEntity
    ) {
        $userRepository->findOneByEmail('email')->shouldBeCalled()->willThrow(UserNotFoundException::class);

        $this->getUserEntityByUserCredentials('email', 'password', 'password', $clientEntity)->shouldReturn(null);
    }

    function it_should_not_get_a_user_by_its_credentials_if_password_not_valid(
        UserRepository $userRepository,
        PasswordVerifier $passwordVerifier,
        TrailmindUser $futureFarmUser,
        ClientEntityInterface $clientEntity
    ) {
        $userRepository->findOneByEmail('email')->shouldBeCalled()->willReturn($futureFarmUser);
        $passwordVerifier->verifyPassword($futureFarmUser, 'wrong-password')->shouldBeCalled()->willReturn(false);

        $this->getUserEntityByUserCredentials('email', 'wrong-password', 'password', $clientEntity)->shouldReturn(null);
    }
}
