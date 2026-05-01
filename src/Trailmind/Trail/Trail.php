<?php

namespace Trailmind\Trail;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
        private Collection $trailPoints = new ArrayCollection()
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

    public function getTrailPoints(): Collection {
        return $this->trailPoints;
    }

    public function addTrailPoint(TrailPoint $trailPoint): void {
        if (!$this->trailPoints->contains($trailPoint)) {
            $this->trailPoints->add($trailPoint);
        }
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
