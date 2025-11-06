<?php

namespace spec\Trailmind\TrailService\TrailPointManager\TrailPointImporter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleXMLElement;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailPoint;
use Trailmind\Trail\TrailRepository;

class TrailPointsImporterSpec extends ObjectBehavior
{
    function let(
        TrailRepository $trailRepository
    ) {
        $this->beConstructedWith(
            $trailRepository
        );
    }

    function it_should_import_an_xml_file(
        Trail $trail,
        TrailRepository $trailRepository
    ) {
        $file = new SimpleXMLElement('
            <gpx version="1.1" creator="Example GPX Importer"
                 xmlns="http://www.topografix.com/GPX/1/1">
                <trk>
                    <name>Example GPX Document</name>
                    <trkseg>
                        <trkpt lat="51.632539546" lon="-2.648426341">
                            <ele>20.43</ele>
                        </trkpt>
                        <trkpt lat="51.632765606" lon="-2.649023635">
                            <ele>20.05</ele>
                        </trkpt>
                        <trkpt lat="51.632941039" lon="-2.649514815">
                            <ele>15.47</ele>
                        </trkpt>
                    </trkseg>
                </trk>
            </gpx>
        ');

        $trail->setTrailPoints(Argument::type('array'))->shouldBeCalled();
        $trail->setRoute('LINESTRING(-2.648426 51.632540,-2.649024 51.632766,-2.649515 51.632941)')->shouldBeCalled();
        $trail->setStartPoint(Argument::type(TrailPoint::class))->shouldBeCalled();
        $trail->setEndPoint(Argument::type(TrailPoint::class))->shouldBeCalled();

        $trailRepository->save($trail)->shouldBeCalled();

        $this->importFile($trail, $file)->shouldReturn(true);
    }
}