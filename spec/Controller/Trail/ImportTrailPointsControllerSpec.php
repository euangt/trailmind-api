<?php

namespace spec\Controller\Trail;

use Dto\Inbound\File\Filename;
use Dto\Outbound\Created;
use PhpSpec\ObjectBehavior;
use SimpleXMLElement;
use Trailmind\Trail\Trail;
use Trailmind\TrailService\TrailPointManager\TrailPointLoader\TrailPointLoader;

class ImportTrailPointsControllerSpec extends ObjectBehavior
{
    function let(
        TrailPointLoader $trailPointLoader
    ) {
        $this->beConstructedWith($trailPointLoader);
    }

    function it_should_return_Created_when_importing_a_trail(
        Trail $trail,
        TrailPointLoader $trailPointLoader
    ) {
        $file = new SimpleXMLElement('<gpx></gpx>');
        $filename = new Filename('filename');

        $trailPointLoader->loadFile('filename')->shouldBeCalled()->willReturn($file);

        $this->importTrailPointsAction($trail, $filename)->shouldReturnAnInstanceOf(Created::class);
    }
}