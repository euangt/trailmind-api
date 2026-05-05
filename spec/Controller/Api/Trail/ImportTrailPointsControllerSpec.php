<?php

namespace spec\Controller\Api\Trail;

use Application\Dto\Inbound\File\Filename;
use Dto\Outbound\Created;
use PhpSpec\ObjectBehavior;
use SimpleXMLElement;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Trailmind\FileService\Exception\UnableToLoadFileException;
use Trailmind\Trail\Trail;
use Trailmind\TrailService\TrailPointManager\TrailPointImporter\TrailPointsImporter;
use Trailmind\TrailService\TrailPointManager\TrailPointLoader\TrailPointLoader;

class ImportTrailPointsControllerSpec extends ObjectBehavior
{
    function let(
        TrailPointLoader $trailPointLoader,
        TrailPointsImporter $trailPointsImporter
    ) {
        $this->beConstructedWith($trailPointLoader, $trailPointsImporter);
    }

    function it_should_return_Created_when_importing_a_trail(
        Trail $trail,
        TrailPointLoader $trailPointLoader,
        TrailPointsImporter $trailPointsImporter
    ) {
        $file = new SimpleXMLElement('<gpx></gpx>');
        $filename = new Filename('filename');

        $trailPointLoader->loadFile('filename')->shouldBeCalled()->willReturn($file);
        $trailPointsImporter->importFile($trail, $file)->shouldBeCalled()->willReturn(true);

        $this->importTrailPointsAction($trail, $filename)->shouldReturnAnInstanceOf(Created::class);
    }

    function it_should_throw_bad_request_when_the_file_is_missing(
        Trail $trail,
        TrailPointLoader $trailPointLoader,
        TrailPointsImporter $trailPointsImporter
    ) {
        $filename = new Filename('missing.gpx');

        $trailPointLoader->loadFile('missing.gpx')->shouldBeCalled()->willThrow(new FileNotFoundException('missing.gpx'));
        $trailPointsImporter->importFile($trail, null)->shouldNotBeCalled();

        $this
            ->shouldThrow(new BadRequestHttpException('missing.gpx'))
            ->during('importTrailPointsAction', [$trail, $filename]);
    }

    function it_should_throw_bad_request_when_the_file_cannot_be_loaded(
        Trail $trail,
        TrailPointLoader $trailPointLoader,
        TrailPointsImporter $trailPointsImporter
    ) {
        $filename = new Filename('broken.gpx');

        $trailPointLoader->loadFile('broken.gpx')->shouldBeCalled()->willThrow(new UnableToLoadFileException('Unable to parse GPX file: broken.gpx'));
        $trailPointsImporter->importFile($trail, null)->shouldNotBeCalled();

        $this
            ->shouldThrow(new BadRequestHttpException('Unable to parse GPX file: broken.gpx'))
            ->during('importTrailPointsAction', [$trail, $filename]);
    }
}
