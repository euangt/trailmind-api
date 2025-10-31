<?php

namespace spec\Controller\Register;

use Dto\Outbound\NoContent;
use PhpSpec\ObjectBehavior;

class RegisterControllerSpec extends ObjectBehavior
{
    function it_should_register_a_user()
    {
        $this->postRegisterAction()->shouldReturnAnInstanceOf(NoContent::class);
    }
}