<?php

namespace Trailmind\Trail;

interface TrailRepository
{
    public function findOneById(string $id): Trail;

    public function save(Trail $trail): void;
}