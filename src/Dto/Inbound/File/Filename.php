<?php

namespace Dto\Inbound\File;

use Symfony\Component\Validator\Constraints as Assert;

class Filename
{
    public function __construct(
        #[Assert\NotBlank]
        public string $filename,
    ) {}
}
