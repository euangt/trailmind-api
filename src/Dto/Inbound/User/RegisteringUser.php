<?php

namespace Dto\Inbound\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegisteringUser
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        public string $password,
        #[Assert\NotBlank]
        public string $username
    ) {}
}
