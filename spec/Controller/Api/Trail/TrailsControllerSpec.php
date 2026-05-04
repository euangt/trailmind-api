<?php

namespace spec\Controller\Api\Trail;

use Dto\Outbound\Success;
use Dto\Outbound\Trail\TrailCollectionBuilder;
use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailRepository;

class TrailsControllerSpec extends ObjectBehavior
{
    function let(
        TrailRepository $trailRepository,
        TrailCollectionBuilder $trailCollectionBuilder
    ) {
        $this->beConstructedWith(
            $trailRepository,
            $trailCollectionBuilder
        );
    }

    function it_should_return_a_trail(
        TrailRepository $trailRepository,
        TrailCollectionBuilder $trailCollectionBuilder,
        Trail $trail1,
        Trail $trail2,
        Trail $trail3,
        Success $success
    ) {
        $trailRepository->findAll()->willReturn([$trail1, $trail2, $trail3]);

        $trailCollectionBuilder->setContext('v1.0_view_trails')->shouldBeCalled()->willReturn($trailCollectionBuilder);
        $trailCollectionBuilder->build([$trail1, $trail2, $trail3])->willReturn($success);

        $this->getTrailAction()->shouldReturn($success);
    }
}