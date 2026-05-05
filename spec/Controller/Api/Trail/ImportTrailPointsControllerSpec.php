<?php

namespace spec\Controller\Api\Trail;

use Application\Dto\Inbound\File\Filename;
use Dto\Outbound\Created;
use PhpSpec\ObjectBehavior;
use SimpleXMLElement;
use Trailmind\Trail\Trail;
use Trailmind\TrailService\TrailPointManager\TrailPointImporter\TrailPointsImporter;
use Trailmind\TrailService\TrailPointManager\TrailPointLoader\TrailPointLoader;
use Trailmind\User\User;

class ImportTrailPointsControllerSpec extends ObjectBehavior
{
    function let(
        TrailPointLoader $trailPointLoader,
        TrailPointsImporter $trailPointsImporter
    ) {
        $this->beConstructedWith($trailPointLoader, $trailPointsImporter);
    }

    function it_should_return_Created_when_importing_a_trail(
        User $authenticatedUser,
        Trail $trail,
        TrailPointLoader $trailPointLoader,
        TrailPointsImporter $trailPointsImporter
    ) {
        $file = new SimpleXMLElement('<gpx></gpx>');
        $filename = new Filename('filename');

        $trailPointLoader->loadFile('filename')->shouldBeCalled()->willReturn($file);
        $trailPointsImporter->importFile($trail, $file)->shouldBeCalled()->willReturn(true);

        $this->importTrailPointsAction($authenticatedUser, $trail, $filename)->shouldReturnAnInstanceOf(Created::class);
    }
}
