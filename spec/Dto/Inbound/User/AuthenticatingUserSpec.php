<?php

namespace spec\Dto\Inbound\User;

use PhpSpec\ObjectBehavior;

class AuthenticatingUserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            'user@user.com',
            'password',
        );
    }

    function it_should_be_constructed_with_a_value_for_email()
    {
        $this->email->shouldBe('user@user.com');
    }

    function it_should_be_constructed_with_a_value_for_password()
    {
        $this->password->shouldBe('password');
    }
}