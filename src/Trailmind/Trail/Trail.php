<?php
namespace Trailmind\Trail;

class Trail
{
    /**
     * @param string $name
     * @param string $difficulty
     * @param float $length
     */
    public function __construct(
        private string $name, 
        private string $difficulty, 
        private float $length
    ) {}

    /**
     * @return string
     */
    public function getName(): string { 
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDifficulty(): string { 
        return $this->difficulty; 
    }

    /**
     * @return float
     */
    public function getLength(): float { 
        return $this->length; 
    }
}
