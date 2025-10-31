<?php

namespace spec\Controller\Register;

use Dto\Inbound\User\RegisteringUser;
use Dto\Outbound\NoContent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Trailmind\User\User;
use Trailmind\User\UserRepository;

class RegisterControllerSpec extends ObjectBehavior
{
    function let(
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $this->beConstructedWith($passwordHasher, $userRepository);
    }

    function it_should_register_a_user(
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $registeringUser = new RegisteringUser('name', 'email', 'password', 'username');
        $passwordHasher->hashPassword(Argument::type(User::class), 'password')->willReturn('hashed-password');

        $userRepository->save(Argument::that(function (User $user) {
            return $user->getEmail() === 'email' &&
                   $user->getName() === 'name' &&
                   $user->getUsername() === 'username' &&
                   $user->getPassword() === 'hashed-password';
        }))->shouldBeCalled();

        $this->postRegisterAction($registeringUser)->shouldReturnAnInstanceOf(NoContent::class);
    }
}