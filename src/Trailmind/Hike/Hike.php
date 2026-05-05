<?php

namespace Trailmind\Hike;

use DateTime;
use Trailmind\Trail\Trail;
use Trailmind\User\User;

class Hike
{
    private $id;    

    public function __construct(
        private Trail $trail,
        private User $user,
        private DateTime $startDate,
        private DateTime $endDate
    ) {}

    public function getTrail(): Trail {
        return $this->trail;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getStartDate(): DateTime {
        return $this->startDate;
    }

    public function getEndDate(): DateTime {
        return $this->endDate;
    }
}