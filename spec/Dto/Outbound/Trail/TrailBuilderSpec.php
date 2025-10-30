<?php

namespace spec\Dto\Outbound\Trail;

use Dto\Outbound\EntityBuilder;
use Dto\Outbound\EntityDto;
use Dto\Outbound\Trail\Decorators\DifficultyDecorator;
use Dto\Outbound\Trail\Decorators\LengthDecorator;
use Dto\Outbound\Trail\Decorators\TrailEntityDecorator;
use Dto\Outbound\Trail\TrailBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Trailmind\Trail\Trail;

class TrailBuilderSpec extends ObjectBehavior
{
    function let(
        DifficultyDecorator $difficultyDecorator,
        LengthDecorator $lengthDecorator
    ) {
        $this->beConstructedWith($difficultyDecorator, $lengthDecorator);
    }

    function it_should_be_an_entity_builder()
    {
        $this->shouldBeAnInstanceOf(EntityBuilder::class);
    }

    function it_should_initialise_a_trail(
        Trail $trail,
        TrailEntityDecorator $trailEntityDecorator1,
        TrailEntityDecorator $trailEntityDecorator2,
        EntityDto $entityDto,
    ) {
        $this->setContext('context');
        $prop = new \ReflectionProperty(TrailBuilder::class, 'decorators');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), [
            $trailEntityDecorator1->getWrappedObject(),
            $trailEntityDecorator2->getWrappedObject()
        ]);

        $trailEntityDecorator1->withTrail($trail)->shouldBeCalled()->willReturn($trailEntityDecorator1);
        $trailEntityDecorator1->decorate(Argument::type(EntityDto::class), 'context')->shouldBeCalled()->willReturn($entityDto);
        $trailEntityDecorator2->withTrail($trail)->shouldBeCalled()->willReturn($trailEntityDecorator2);
        $trailEntityDecorator2->decorate(Argument::type(EntityDto::class), 'context')->shouldBeCalled()->willReturn($entityDto);

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