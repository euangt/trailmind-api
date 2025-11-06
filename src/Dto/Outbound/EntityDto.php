<?php

namespace Dto\Outbound;

use JsonSerializable;

abstract class EntityDto implements Jsonable, JsonSerializable
{
    protected $data = [];

    public function add(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}