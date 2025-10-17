<?php

namespace spec\Trailmind\Trail;

use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;

class TrailSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Appalachian Trail', 'Hard', 2190.0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Trail::class);
    }

    function it_should_know_its_name()
    {
        $this->getName()->shouldReturn('Appalachian Trail');
    }

    function it_should_know_its_difficulty()
    {
        $this->getDifficulty()->shouldReturn('Hard');
    }

    function it_should_know_its_length()
    {
        $this->getLength()->shouldReturn(2190.0);
    }
}