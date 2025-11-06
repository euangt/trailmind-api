<?php

namespace spec\Controller\Trail;

use Dto\Inbound\File\Filename;
use Dto\Outbound\Created;
use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;

class ImportTrailPointsControllerSpec extends ObjectBehavior
{
    function it_should_return_Created_when_importing_a_trail(
        Trail $trail,
    ) {
        $filename = new Filename('filename');

        $this->importTrailPointsAction($trail, $filename)->shouldReturnAnInstanceOf(Created::class);
    }
}