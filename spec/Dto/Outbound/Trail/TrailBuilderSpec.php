<?php

namespace spec\Dto\Outbound\Trail;

use Dto\Outbound\EntityBuilder;
use Dto\Outbound\EntityDto;
use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;

class TrailBuilderSpec extends ObjectBehavior
{
    function it_should_be_an_entity_builder()
    {
        $this->shouldBeAnInstanceOf(EntityBuilder::class);
    }

    function it_should_initialise_a_trail(
        Trail $trail,
    ) {
        $this->setContext('context');

        $trail->getId()->willReturn('1234');
        $trail->getName()->willReturn('Trail Name');

        $this->build($trail)->shouldReturnAnInstanceOf(EntityDto::class);
    }

    function it_should_not_allow_any_other_initialisable()
    {
        $this->shouldThrow(\UnexpectedValueException::class)->duringBuild('not a trail');
    }

    function it_should_allow_a_context_to_be_set()
    {
        $this->setContext('context')->shouldReturn($this);
    }
}