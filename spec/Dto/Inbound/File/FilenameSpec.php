<?php

namespace spec\Dto\Inbound\File;

use PhpSpec\ObjectBehavior;

class FilenameSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            'filename',
        );
    }

    function it_should_be_constructed_with_a_value_for_filename()
    {
        $this->filename->shouldBe('filename');
    }
}