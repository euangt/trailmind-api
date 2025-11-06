<?php

namespace spec\Trailmind\TrailService\TrailPointManager\TrailPointImporter;

use PhpSpec\ObjectBehavior;
use SimpleXMLElement;
use Trailmind\Trail\Trail;

class TrailPointsImporterSpec extends ObjectBehavior
{
    function it_should_import_an_xml_file(
        Trail $trail
    ) {
        $file = new SimpleXMLElement('<gpx><trk><name>Example GPX Document</name></trk></gpx>');


        $this->importFile($trail, $file)->shouldReturn(true);
    }
}