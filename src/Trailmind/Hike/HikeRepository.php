<?php

namespace Trailmind\Hike;

use Trailmind\User\User;

interface HikeRepository
{
    public function findOneById(string $id): ?Hike;

    public function save(Hike $hike): void;

    public function findAllByUser(User $user): array;
}