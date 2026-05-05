<?php

namespace spec\Dto\Outbound\Hike\Decorators;

use Dto\Outbound\EntityDecorator;
use Dto\Outbound\EntityDto;
use Dto\Outbound\Hike\Decorators\HikeEntityDecorator;
use Dto\Outbound\Hike\Decorators\TrailDecorator;
use Dto\Outbound\Success;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Trailmind\Hike\Hike;
use Trailmind\Trail\Trail;

class TrailDecoratorSpec extends ObjectBehavior
{
    function it_should_be_an_entity_decorator()
    {
        $this->shouldBeAnInstanceOf(EntityDecorator::class);
    }

    function it_should_be_a_hike_entity_decorator()
    {
        $this->shouldBeAnInstanceOf(HikeEntityDecorator::class);
    }

    function it_should_decorate_the_trail_onto_a_hike_entity(
        Success $entityDto,
        Hike $hike,
        Trail $trail,
    ) {
        $prop = new \ReflectionProperty(HikeEntityDecorator::class, 'hike');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), $hike->getWrappedObject());

        $hike->getTrail()->willReturn($trail);
        $trail->getId()->willReturn('trail-uuid-1234');
        $trail->getName()->willReturn('Appalachian Trail');

        $this->decorate($entityDto, 'v1.0_record_hike')->shouldReturnAnInstanceOf(EntityDto::class);
    }

    function it_should_not_decorate_for_an_invalid_context(
        Success $entityDto,
        Hike $hike,
    ) {
        $prop = new \ReflectionProperty(HikeEntityDecorator::class, 'hike');
        $prop->setAccessible(true);
        $prop->setValue($this->getWrappedObject(), $hike->getWrappedObject());

        $entityDto->add(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->decorate($entityDto, 'invalid_context')->shouldReturnAnInstanceOf(EntityDto::class);
    }
}
