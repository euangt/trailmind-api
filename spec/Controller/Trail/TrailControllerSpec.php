<?php

namespace spec\Controller\Trail;

use Dto\Outbound\Success;
use Dto\Outbound\Trail\TrailBuilder;
use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;

class TrailControllerSpec extends ObjectBehavior
{
    function let(
        TrailBuilder $trailBuilder
    ) {
        $this->beConstructedWith(
            $trailBuilder
        );
    }

    function it_should_return_a_trail(
        TrailBuilder $trailBuilder,
        Trail $trail,
        Success $success
    ) {
        $trailBuilder->setContext('v1.0_view_trail')->shouldBeCalled()->willReturn($trailBuilder);
        $trailBuilder->build($trail)->willReturn($success);

        $this->getTrailAction($trail)->shouldReturn($success);
    }
}