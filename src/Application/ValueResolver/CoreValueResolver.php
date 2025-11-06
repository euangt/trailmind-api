<?php

namespace Application\ValueResolver;

use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Value resolvers share a lot of core behaviour such as determining whether the
 * value is in the url or payload, determining the default value, whether the value is nullable
 * and so on.  It makes sense to abstract that away to avoid massive duplication.
 */
class CoreValueResolver
{
    /**
     * Get the options from the customisable value resolver
     */
    protected function getOptions(ArgumentMetadata $argument): array
    {
        if ($argument->getAttributes()[0] instanceof CustomisableValueResolver) {
            return $argument->getAttributes()[0]->getOptions();
        } else {
            return [];
        }
    }

    /**
     * Determine if this value can have a null value returned
     */
    protected function isNullable(array $options): bool
    {
        return array_key_exists('nullable', $options) && $options['nullable'] === true;
    }

    /**
     * Determine if this value can have a default value returned
     */
    protected function hasDefaultValue(array $options): bool
    {
        return array_key_exists('defaultValue', $options);
    }

    /**
     * Get the default value that should be returned if no value is found
     */
    protected function getDefaultValue(array $options): mixed
    {
        return $options['defaultValue'];
    }

    /**
     * Determine if there is a specific key that we are looking for in the request
     */
    protected function hasRequestKey(array $options): bool
    {
        return array_key_exists('key', $options) && ! is_null($options['key']);
    }

    /**
     * Get the key that we are looking for in the request
     */
    protected function getRequestKey(array $options): string
    {
        return $options['key'];
    }

    /**
     * Determine the argument name to query the request with
     */
    protected function determineArgumentName(ArgumentMetadata $argument): string
    {
        if ($this->hasRequestKey($this->getOptions($argument))) {
            return $this->getRequestKey($this->getOptions($argument));
        }

        return $argument->getName();
    }

    /**
     * Determine if a negative value is allowed
     */
    protected function allowsNegative(array $options): bool
    {
        if (array_key_exists('allowsNegative', $options) && $options['allowsNegative'] === false) {
            return false;
        } else {
            return true;
        }
    }
}