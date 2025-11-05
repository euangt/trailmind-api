<?php

namespace spec\Trailmind\Access;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientSpec extends ObjectBehavior
{
    const ID = '12345';
    const NAME = 'Future Farm Client';

    function let()
    {
        $this->beConstructedWith(self::ID, self::NAME);
    }

    function it_should_be_a_UserInterface()
    {
        $this->shouldBeAnInstanceOf(UserInterface::class);
    }

    function it_should_be_constructed_with_an_id()
    {
        $this->getId()->shouldReturn(self::ID);
    }

    function it_should_be_constructed_with_a_name()
    {
        $this->getName()->shouldReturn(self::NAME);
    }

    function it_should_allow_a_secret_to_be_set()
    {
        $this->getSecret()->shouldReturn(null);
        $this->setSecret('secret');
        $this->getSecret()->shouldReturn('secret');
    }

    function it_should_allow_a_redirect_to_be_set()
    {
        $this->getRedirect()->shouldReturn(null);
        $this->setRedirect('/over/here');
        $this->getRedirect()->shouldReturn('/over/here');
    }

    function it_should_be_active_by_default()
    {
        $this->isActive()->shouldBe(true);
    }

    function it_should_allow_itself_to_be_activated_and_deactivated()
    {
        $this->deactivate();
        $this->isActive()->shouldBe(false);
        $this->activate();
        $this->isActive()->shouldBe(true);
    }

    function it_should_require_verfication_by_default()
    {
        $this->requiresVerification()->shouldReturn(true);
    }

    function it_should_allow_required_verification_status_to_be_changed()
    {
        $this->setRequiresVerification(false);
        $this->requiresVerification()->shouldReturn(false);
    }

    function it_should_allow_roles_to_be_added()
    {
        $this->getRoles()->shouldReturn([]);
        $this->addRole('ROLE_CLIENT');
        $this->getRoles()->shouldReturn(['ROLE_CLIENT']);
    }
}
