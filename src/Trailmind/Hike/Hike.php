<?php

namespace Trailmind\Hike;

use DateTimeImmutable;
use Trailmind\Trail\Trail;
use Trailmind\User\User;

class Hike
{
    private $id;

    public function __construct(
        private Trail $trail,
        private User $user,
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate,
    ) {}

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getTrail(): Trail
    {
        return $this->trail;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }
}
