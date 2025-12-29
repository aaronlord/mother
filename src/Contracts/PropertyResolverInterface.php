<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Reflection\PropertyDefinition;

/**
 * @template T of object
 */
interface PropertyResolverInterface
{
    /**
     * Get property definitions for a class.
     *
     * @param class-string<T> $class
     * @return list<PropertyDefinition>
     */
    public function for(string $class): array;
}
