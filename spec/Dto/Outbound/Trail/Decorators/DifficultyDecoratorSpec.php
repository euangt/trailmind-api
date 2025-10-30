<?php

namespace spec\Dto\Outbound\Trail\Decorators;

use Dto\Outbound\EntityDecorator;
use Dto\Outbound\EntityDto;
use Dto\Outbound\Success;
use Dto\Outbound\Trail\Decorators\TrailEntityDecorator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Trailmind\Trail\Trail;

class DifficultyDecoratorSpec extends ObjectBehavior
{
    function it_should_be_an_entity_decorator()
    {
        $this->shouldBeAnInstanceOf(EntityDecorator::class);
    }

    function it_should_be_a_trail_entity_decorator()
    {
        $this->shouldBeAnInstanceOf(TrailEntityDecorator::class);
    }

    function it_should_decorate_a_trail_entity(
        Success $entityDto,
        Trail $trail
    ) {
        $prop = new \ReflectionProperty(TrailEntityDecorator::class, 'trail');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), $trail->getWrappedObject());

        $trail->getDifficulty()->willReturn('hard');

        $this->decorate(
            $entityDto,
            'v1.0_view_trail',
        )->shouldReturnAnInstanceOf(EntityDto::class);
    }

    function it_should_not_decorate_a_trail_entity_for_an_invalid_context(
        Success $entityDto,
        Trail $trail
    ) {
        $prop = new \ReflectionProperty(TrailEntityDecorator::class, 'trail');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), $trail->getWrappedObject());

        $entityDto->add(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->decorate(
            $entityDto,
            'invalid_context',
        )->shouldReturnAnInstanceOf(EntityDto::class);
    }
}