<?php

namespace spec\Trailmind\Access;

use PhpSpec\ObjectBehavior;
use Trailmind\Access\AccessToken;
use DateTimeImmutable;

class RefreshTokenSpec extends ObjectBehavior
{
    const ID = '54321';

    function let(AccessToken $accessToken, DateTimeImmutable $expires)
    {
        $this->beConstructedWith(self::ID, $accessToken, $expires);
    }

    function it_should_be_constructed_with_an_AccessToken(AccessToken $accessToken)
    {
        $this->getAccessToken()->shouldBe($accessToken);
    }

    function it_should_be_constructed_with_an_expiry_date(DateTimeImmutable $expires)
    {
        $this->getExpiresAt()->shouldBe($expires);
    }

    function it_should_be_constructed_with_an_id()
    {
        $this->getId()->shouldBe(self::ID);
    }

    function it_should_be_able_to_be_revoked()
    {
        $this->isRevoked()->shouldReturn(false);
        $this->revoke();
        $this->isRevoked()->shouldReturn(true);
    }
}
