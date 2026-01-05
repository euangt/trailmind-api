<?php

namespace spec\Dto\Outbound\Trail;

use Dto\Outbound\CollectionBuilder;
use Dto\Outbound\EntityDto;
use Dto\Outbound\Success;
use Dto\Outbound\Trail\TrailBuilder;
use Dto\Outbound\Trail\TrailCollectionBuilder;
use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;

class TrailCollectionBuilderSpec extends ObjectBehavior
{
    function let(TrailBuilder $trailBuilder)
    {
        $this->beConstructedWith($trailBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TrailCollectionBuilder::class);
    }

    function it_should_be_a_collectionBuilder()
    {
        $this->shouldBeAnInstanceOf(CollectionBuilder::class);
    }

    function it_should_initialise_a_collection_of_trails(
        TrailBuilder $trailBuilder,
        Trail $trail1,
        Trail $trail2,
        Success $entityDto1,
        Success $entityDto2
    ) {
        $this->setContext('context');

        $trailBuilder->setContext('context')->willReturn($trailBuilder);
        $trailBuilder->build($trail1)->willReturn($entityDto1);
        $trailBuilder->build($trail2)->willReturn($entityDto2);

        $this->build([$trail1, $trail2])->shouldReturnAnInstanceOf(EntityDto::class);
    }

    function it_should_not_allow_any_other_initialisable() {
        $this->shouldThrow(\UnexpectedValueException::class)->duringBuild('not a trail');
    }

    function it_should_allow_a_context_to_be_set() {
        $this->setContext('context')->shouldReturn($this);
    }
}