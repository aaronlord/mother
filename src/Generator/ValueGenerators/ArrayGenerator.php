<?php

declare(strict_types=1);

namespace Lord\Mother\Generator\ValueGenerators;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

readonly class ArrayGenerator implements ValueGeneratorInterface
{
    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->type === 'array';
    }

    public function generate(PropertyDefinition $property, Options $options): mixed
    {
        return [];
    }
}
