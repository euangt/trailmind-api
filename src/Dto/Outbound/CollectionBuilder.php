<?php

namespace Dto\Outbound;

use Dto\Outbound\EntityBuilder;
use UnexpectedValueException;

abstract class CollectionBuilder extends EntityBuilder
{
    /**
     * @param mixed $initialisable
     *
     * @return mixed
     *
     * @throws UnexpectedValueException
     */
    protected function verifyEntity($initialisable): mixed
    {
        if (!is_array($initialisable)) {
            throw new UnexpectedValueException("Value passed to builder was not a valid array");
        }

        return $initialisable;
    }
}