<?php

namespace spec\Trailmind\Trail;

use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailPoint;

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

    function it_should_know_its_id()
    {
        $prop = new \ReflectionProperty(Trail::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), '1234');

        $this->getId()->shouldReturn('1234');
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

    function it_should_get_and_set_trail_points(
        TrailPoint $trailPoint1,
        TrailPoint $trailPoint2,
        TrailPoint $trailPoint3
    )
    {
        $this->setTrailPoints([$trailPoint1, $trailPoint2]);
        $this->getTrailPoints()->shouldReturn([$trailPoint1, $trailPoint2]);

        $this->addTrailPoint($trailPoint3);
        $this->getTrailPoints()->shouldReturn([$trailPoint1, $trailPoint2, $trailPoint3]);
    }
}