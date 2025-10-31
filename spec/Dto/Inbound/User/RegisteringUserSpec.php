<?php

namespace spec\Dto\Inbound\User;

use PhpSpec\ObjectBehavior;

class RegisteringUserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            'farmer',
            'farmer@farm.com',
            'password',
            'username'
        );
    }

    function it_should_be_constructed_with_a_value_for_name()
    {
        $this->name->shouldBe('farmer');
    }

    function it_should_be_constructed_with_a_value_for_email()
    {
        $this->email->shouldBe('farmer@farm.com');
    }

    function it_should_be_constructed_with_a_value_for_password()
    {
        $this->password->shouldBe('password');
    }

    function it_should_be_constructed_with_a_value_for_username()
    {
        $this->username->shouldBe('username');
    }
}