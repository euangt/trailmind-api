<?php

namespace Dto\Outbound;

use JsonSerializable;

abstract class EntityDto implements Jsonable, JsonSerializable
{
    protected $data = [];

    /**
     * @param string $key
     * @param mixed  $value
     * 
     * @return void
     */
    public function add(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}