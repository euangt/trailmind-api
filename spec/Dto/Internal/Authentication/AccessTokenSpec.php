<?php

namespace spec\Dto\Internal\Authentication;

use PhpSpec\ObjectBehavior;

class AccessTokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('access_token_value', 'refresh_token_value', 3600.0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Dto\Internal\Authentication\AccessToken');
    }

    function it_has_an_access_token()
    {
        $this->accessToken->shouldBe('access_token_value');
    }

    function it_has_a_refresh_token()
    {
        $this->refreshToken->shouldBe('refresh_token_value');
    }

    function it_has_an_expires_in()
    {
        $this->expiresIn->shouldBe(3600.0);
    }
}