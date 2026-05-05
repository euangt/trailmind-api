<?php

namespace spec\Application\Dto\Inbound\User;

use PhpSpec\ObjectBehavior;

class ReauthenticatingUserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('refresh-token');
    }

    function it_should_be_constructed_with_a_value_for_refresh_token()
    {
        $this->refreshToken->shouldBe('refresh-token');
    }
}
