<?php

namespace spec\Trailmind\AuthenticationService;

use PhpSpec\ObjectBehavior;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Trailmind\AuthenticationService\PasswordVerifier;
use Trailmind\User\User;

class PasswordVerifierSpec extends ObjectBehavior
{
    function let(UserPasswordHasherInterface $passwordHasher)
    {
        $this->beConstructedWith($passwordHasher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PasswordVerifier::class);
    }

    function it_returns_true_when_password_is_valid(
        UserPasswordHasherInterface $passwordHasher,
        User $user
    ) {
        $plainTextPassword = 'correct-password';
        
        $user->getPassword()->willReturn('hashed-password');
        $passwordHasher->isPasswordValid($user, $plainTextPassword)->willReturn(true);

        $this->verifyPassword($user, $plainTextPassword)->shouldReturn(true);
    }

    function it_returns_false_when_password_is_invalid(
        UserPasswordHasherInterface $passwordHasher,
        User $user
    ) {
        $plainTextPassword = 'wrong-password';
        
        $user->getPassword()->willReturn('hashed-password');
        $passwordHasher->isPasswordValid($user, $plainTextPassword)->willReturn(false);

        $this->verifyPassword($user, $plainTextPassword)->shouldReturn(false);
    }

    function it_returns_false_when_user_has_no_password(
        UserPasswordHasherInterface $passwordHasher,
        User $user
    ) {
        $user->getPassword()->willReturn(null);

        $this->verifyPassword($user, 'any-password')->shouldReturn(false);
        
        $passwordHasher->isPasswordValid($user, 'any-password')->shouldNotBeCalled();
    }
}
