<?php

namespace Controller\Register;

use Dto\Outbound\NoContent;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController
{
    #[Route('/v1.0/register', methods: ['POST'], name: 'api_v1.0_register')]
    public function postRegisterAction(): NoContent {
        return new NoContent();
    }
}