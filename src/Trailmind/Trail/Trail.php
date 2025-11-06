<?php

namespace Trailmind\Trail;

class Trail
{
    private $id;

    public function __construct(
        private string $name,
        private string $difficulty,
        private float $length
    ) {}

    public function getId(): ?string {
        return $this->id;
    }

    public function getName(): string { 
        return $this->name;
    }

    public function getDifficulty(): string { 
        return $this->difficulty; 
    }

    public function getLength(): float { 
        return $this->length; 
    }
}
