<?php

namespace spec\Trailmind\Trail;

use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailPoint;

class TrailPointSpec extends ObjectBehavior
{
    function let(
        Trail $trail
    ) {
        $this->beConstructedWith($trail, 34.0522, -118.2437, 305.0, 1);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TrailPoint::class);
    }

    function it_should_know_its_id()
    {
        $prop = new \ReflectionProperty(TrailPoint::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), '5678');

        $this->getId()->shouldReturn('5678');
    }

    function it_should_know_its_trail(Trail $trail)
    {
        $this->getTrail()->shouldReturn($trail);
    }

    function it_should_know_its_latitude()
    {
        $this->getLatitude()->shouldReturn(34.0522);
    }

    function it_should_know_its_longitude()
    {
        $this->getLongitude()->shouldReturn(-118.2437);
    }

    function it_should_know_its_elevation()
    {
        $this->getElevation()->shouldReturn(305.0);
    }

    function it_should_know_its_sequence_number()
    {
        $this->getSequenceNumber()->shouldReturn(1);
    }

    function it_should_get_and_set_geom()
    {
        $this->setGeom('POINT(34.0522 -118.2437)');
        $this->getGeom()->shouldReturn('POINT(34.0522 -118.2437)');
    }
}