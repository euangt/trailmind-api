<?php

namespace Application\Dto\Inbound\User;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ReauthenticatingUser
{
    public function __construct(
        #[Assert\NotBlank]
        #[SerializedName('refresh_token')]
        public string $refreshToken,
    ) {}
}
