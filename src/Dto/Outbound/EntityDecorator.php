<?php

namespace Dto\Outbound;

abstract class EntityDecorator
{
    protected const CONTEXTS = [];

    /**
     * @var DecoratorBundle
     */
    protected $decoratorBundle;

    abstract function decorate(
        EntityDto $entityDto,
        string $context
    ): EntityDto;

    public function shouldDecorate(string $context): bool
    {
        return in_array($context, static::CONTEXTS);
    }
}