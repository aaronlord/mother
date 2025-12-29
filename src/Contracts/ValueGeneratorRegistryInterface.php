<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

interface ValueGeneratorRegistryInterface
{
    /**
     * Get all registered generators.
     *
     * @return list<ValueGeneratorInterface>
     */
    public function all(): array;

    /**
     * Prepend one or more generators to the beginning of the registry.
     */
    public function unshift(ValueGeneratorInterface ...$generator): self;

    /**
     * Append one or more generators to the end of the registry.
     */
    public function push(ValueGeneratorInterface ...$generator): self;

    public function generate(PropertyDefinition $property, Options $options): mixed;
}
