<?php

namespace spec\Dto\Outbound\Hike;

use Dto\Outbound\CollectionBuilder;
use Dto\Outbound\Created;
use Dto\Outbound\EntityDto;
use Dto\Outbound\Hike\HikeBuilder;
use Dto\Outbound\Hike\HikeCollectionBuilder;
use PhpSpec\ObjectBehavior;
use Trailmind\Hike\Hike;

class HikeCollectionBuilderSpec extends ObjectBehavior
{
    function let(HikeBuilder $hikeBuilder)
    {
        $this->beConstructedWith($hikeBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HikeCollectionBuilder::class);
    }

    function it_should_be_a_collection_builder()
    {
        $this->shouldBeAnInstanceOf(CollectionBuilder::class);
    }

    function it_should_build_a_collection_of_hikes(
        HikeBuilder $hikeBuilder,
        Hike $hike1,
        Hike $hike2,
        Created $dto1,
        Created $dto2,
    ) {
        $this->setContext('v1.0_view_hikes');

        $hikeBuilder->setContext('v1.0_view_hikes')->willReturn($hikeBuilder);
        $hikeBuilder->build($hike1)->willReturn($dto1);
        $hikeBuilder->build($hike2)->willReturn($dto2);

        $this->build([$hike1, $hike2])->shouldReturnAnInstanceOf(EntityDto::class);
    }

    function it_should_not_accept_a_non_array_value()
    {
        $this->shouldThrow(\UnexpectedValueException::class)->duringBuild('not an array');
    }

    function it_should_allow_a_context_to_be_set()
    {
        $this->setContext('v1.0_view_hikes')->shouldReturn($this);
    }
}
