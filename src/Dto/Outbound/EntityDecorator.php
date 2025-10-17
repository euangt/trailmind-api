<?php

namespace Dto\Outbound;

use Dto\Outbound\EntityDto;
use Dto\Outgoing\Dto;

abstract class EntityDecorator
{
    protected const CONTEXTS = [];

    /**
     * @var DecoratorBundle
     */
    protected $decoratorBundle;

    /**
     * @param Dto    $dto
     * @param string $context
     * 
     * @return EntityDto
     */
    abstract function decorate(
        EntityDto $entityDto, 
        string $context
    ): EntityDto;

    /**
     * @param string $context
     * 
     * @return bool
     */
    public function shouldDecorate(string $context): bool
    {
        return in_array($context, static::CONTEXTS);
    }
}