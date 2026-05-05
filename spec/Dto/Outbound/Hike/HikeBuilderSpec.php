<?php

namespace spec\Dto\Outbound\Hike;

use DateTimeImmutable;
use Dto\Outbound\EntityBuilder;
use Dto\Outbound\EntityDto;
use Dto\Outbound\Hike\HikeBuilder;
use PhpSpec\ObjectBehavior;
use Trailmind\Hike\Hike;
use Trailmind\Trail\Trail;

class HikeBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HikeBuilder::class);
    }

    function it_should_be_an_entity_builder()
    {
        $this->shouldBeAnInstanceOf(EntityBuilder::class);
    }

    function it_should_build_a_hike(Hike $hike, Trail $trail)
    {
        $hike->getId()->willReturn(1);
        $hike->getTrail()->willReturn($trail);
        $hike->getStartDate()->willReturn(new DateTimeImmutable('2024-06-01 08:00:00'));
        $hike->getEndDate()->willReturn(new DateTimeImmutable('2024-06-01 16:00:00'));
        $trail->getId()->willReturn('trail-uuid-1234');
        $trail->getName()->willReturn('Appalachian Trail');

        $this->setContext('v1.0_record_hike');
        $this->build($hike)->shouldReturnAnInstanceOf(EntityDto::class);
    }

    function it_should_not_accept_a_non_hike_value()
    {
        $this->shouldThrow(\UnexpectedValueException::class)->duringBuild('not a hike');
    }

    function it_should_allow_a_context_to_be_set()
    {
        $this->setContext('v1.0_record_hike')->shouldReturn($this);
    }
}
