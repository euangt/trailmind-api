<?php

namespace Trailmind\Trail;

interface TrailRepository
{
    /**
     * @param string $id
     * 
     * @return Trail
     */
    public function findOneById(string $id): Trail;

    /**
     * @param Trail $trail
     */
    public function save(Trail $trail): void;
}