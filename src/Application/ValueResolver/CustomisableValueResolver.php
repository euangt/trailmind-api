<?php

namespace Application\ValueResolver;

use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;

#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class CustomisableValueResolver extends ValueResolver
{
    /**
     * @param class-string<ValueResolverInterface>|string $resolver
     */
    public function __construct(
        public string $resolver,
        public bool $disabled = false,
        public array $options = []
    ) {
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}