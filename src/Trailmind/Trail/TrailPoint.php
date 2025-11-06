<?php

namespace Trailmind\Trail;

class TrailPoint
{
    private $id;
    private $geom;

    public function __construct(
        private Trail $trail,
        private float $latitude,
        private float $longitude,
        private float|null $elevation,
        private int $sequenceNumber
    ) {}

    public function getId(): ?string {
        return $this->id;
    }

    public function getTrail(): Trail
    {
        return $this->trail;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getElevation(): float|null
    {
        return $this->elevation;
    }

    public function getSequenceNumber(): int|null
    {
        return $this->sequenceNumber;
    }

    public function getGeom(): mixed
    {
        return $this->geom;
    }

    public function setGeom(mixed $geom): void
    {
        $this->geom = $geom;
    }
}