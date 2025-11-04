<?php

namespace Dto\Internal\Authentication;

use Symfony\Component\Validator\Constraints as Assert;

class AccessToken
{
    public function __construct(
        #[Assert\NotBlank]
        public string $accessToken,
        #[Assert\NotBlank]
        public string $refreshToken,
        #[Assert\NotBlank]
        public float $expiresIn
    ) {}
}
