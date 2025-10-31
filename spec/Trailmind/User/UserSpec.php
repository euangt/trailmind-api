<?php

namespace spec\Trailmind\User;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Trailmind\User\User;

class UserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('john.doe@example.com', 'John Doe', ['ROLE_USER']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(User::class);
    }

    function it_should_implement_UserInterface()
    {
        $this->shouldBeAnInstanceOf(UserInterface::class);
    }

    function it_should_implement_PasswordAuthenticatedUserInterface()
    {
        $this->shouldBeAnInstanceOf(PasswordAuthenticatedUserInterface::class);
    }

    function it_should_know_its_id()
    {
        $prop = new \ReflectionProperty(User::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), '1234');

        $this->getId()->shouldReturn('1234');
    }

    function it_should_know_its_email()
    {
        $this->getEmail()->shouldReturn('john.doe@example.com');
    }

    function it_should_know_its_name()
    {
        $this->getName()->shouldReturn('John Doe');
    }

    function it_should_know_its_roles()
    {
        $this->getRoles()->shouldReturn(['ROLE_USER']);
    }

    function it_should_set_password()
    {
        $this->setPassword('hashed_password');
        $this->getPassword()->shouldReturn('hashed_password');
    }

    function it_should_set_roles()
    {
        $this->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $this->getRoles()->shouldReturn(['ROLE_ADMIN', 'ROLE_USER']);
    }

    function it_should_add_role()
    {
        $this->addRole('ROLE_ADMIN');
        $this->getRoles()->shouldReturn(['ROLE_USER', 'ROLE_ADMIN']);
    }

    function it_should_not_duplicate_roles_when_adding()
    {
        $this->addRole('ROLE_USER');
        $this->getRoles()->shouldReturn(['ROLE_USER']);
    }

    function it_should_check_if_has_role()
    {
        $this->hasRole('ROLE_USER')->shouldReturn(true);
        $this->hasRole('ROLE_ADMIN')->shouldReturn(false);
    }

    function it_should_use_id_as_user_identifier()
    {
        $prop = new \ReflectionProperty(User::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), '1234');

        $this->getUserIdentifier()->shouldReturn('1234');
    }

    function it_should_erase_credentials()
    {
        $this->eraseCredentials()->shouldReturn(null);
    }
}