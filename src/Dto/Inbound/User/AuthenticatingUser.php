<?php

namespace Dto\Inbound\User;

use Symfony\Component\Validator\Constraints as Assert;

class AuthenticatingUser
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        public string $password
    ) {}
}
