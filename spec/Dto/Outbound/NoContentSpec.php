<?php

namespace spec\Dto\Outbound;

use Dto\Outbound\EntityDto;
use Dto\Outbound\Jsonable;
use PhpSpec\ObjectBehavior;

class NoContentSpec extends ObjectBehavior
{
    function it_should_be_a_Dto()
    {
        $this->shouldBeAnInstanceOf(EntityDto::class);
    }
    
    function it_should_be_Jsonable()
    {
        $this->shouldBeAnInstanceOf(Jsonable::class);
    }
    
    function it_should_have_a_201_status_code()
    {
        $this->getStatusCode()->shouldBe(204);
    }
}