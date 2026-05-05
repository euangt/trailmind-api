<?php

namespace spec\Trailmind\Hike;

use DateTimeImmutable;
use PhpSpec\ObjectBehavior;
use Trailmind\Hike\Hike;
use Trailmind\Trail\Trail;
use Trailmind\User\User;

class HikeSpec extends ObjectBehavior
{
    function let(Trail $trail, User $user)
    {
        $this->beConstructedWith(
            $trail,
            $user,
            new DateTimeImmutable('2024-06-01 08:00:00'),
            new DateTimeImmutable('2024-06-01 16:00:00'),
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Hike::class);
    }

    function it_should_know_its_trail(Trail $trail)
    {
        $this->getTrail()->shouldReturn($trail);
    }

    function it_should_know_its_user(User $user)
    {
        $this->getUser()->shouldReturn($user);
    }

    function it_should_know_its_start_date()
    {
        $this->getStartDate()->shouldBeLike(new DateTimeImmutable('2024-06-01 08:00:00'));
    }

    function it_should_know_its_end_date()
    {
        $this->getEndDate()->shouldBeLike(new DateTimeImmutable('2024-06-01 16:00:00'));
    }
}
