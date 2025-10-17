<?php

namespace Dto\Outbound;

use UnexpectedValueException;

abstract class EntityBuilder
{
    protected const EXPECTED_CLASS = '';

    /**
     * @var EntityDto
     */
    protected $dto;

    /**
     * @var array
     */
    protected array $decorators = [];

    /**
     * @var string
     */
    protected $context;

    public function __construct()
    {
        $this->decorators = [];
    }

    /**
     * @param mixed $initialisable
     * 
     * @return EntityDto
     */
    public function build(mixed $initialisable): EntityDto
    {
        return $this->initialise(static::verifyEntity($initialisable))
                    ->decorate()
                    ->render();
    }

    /**
     * @param mixed $initialisable
     *
     * @return self
     */
    abstract protected function initialise($initialisable): self;

    /**
     * @return self
     */
    protected function decorate(): self
    {
        return $this;
    }

    /**
     * @return EntityDto
     */
    protected function render(): EntityDto
    {
        return $this->dto;
    }

    /**
     * @param mixed $initialisable
     *
     * @return mixed
     *
     * @throws UnexpectedValueException
     */
    protected function verifyEntity($initialisable): mixed
    {
        if (!is_a($initialisable, static::EXPECTED_CLASS)) {
            throw new \UnexpectedValueException("Value passed to builder was not a valid instance of " . static::EXPECTED_CLASS);
        }

        return $initialisable;
    }

    /**
     * @param string $context
     *
     * @return self
     */
    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function withoutKeys(): self
    {
        $this->dto->hideKeys();

        return $this;
    }
}