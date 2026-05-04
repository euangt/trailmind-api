<?php

namespace spec\Trailmind\Hike;

use DateTime;
use PhpSpec\ObjectBehavior;
use Trailmind\Hike\Hike;
use Trailmind\Trail\Trail;
use Trailmind\User\User;

class HikeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Hike::class);
    }
    
    function let(
        Trail $trail,
        User $user
    ) {
        $this->beConstructedWith($trail, $user, new DateTime('2024-01-01'), new DateTime('2024-01-02'));
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
        $this->getStartDate()->shouldBeLike(new DateTime('2024-01-01'));
    }
    
    function it_should_know_its_end_date()
    {
        $this->getEndDate()->shouldBeLike(new DateTime('2024-01-02'));
    }
}