<?php

namespace Trailmind\Trail;

use Doctrine\ORM\PersistentCollection;

class Trail
{
    private $id;
    private ?TrailPoint $startPoint = null;
    private ?TrailPoint $endPoint = null;
    private ?string $route = null;

    public function __construct(
        private string $name,
        private string $difficulty,
        private float $length,
        private array|PersistentCollection $trailPoints = []
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

    public function getTrailPoints(): array {
        return $this->trailPoints;
    }

    public function addTrailPoint(TrailPoint $trailPoint): void {
        $this->trailPoints[] = $trailPoint;
    }

    public function setTrailPoints(array $trailPoints): void {
        $this->trailPoints = $trailPoints;
    }

    public function getStartPoint(): ?TrailPoint {
        return $this->startPoint;
    }

    public function getEndPoint(): ?TrailPoint {
        return $this->endPoint;
    }

    public function setStartPoint(TrailPoint $startPoint): void {
        $this->startPoint = $startPoint;
    }

    public function setEndPoint(TrailPoint $endPoint): void {
        $this->endPoint = $endPoint;
    }

    public function getRoute(): ?string {
        return $this->route;
    }

    public function setRoute(?string $route): void {
        $this->route = $route;
    }
}
