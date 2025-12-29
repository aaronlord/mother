<?php

declare(strict_types=1);

namespace Lord\Mother\Generator\ValueGenerators;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

readonly class MixedGenerator implements ValueGeneratorInterface
{
    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->type === 'mixed';
    }

    public function generate(PropertyDefinition $property, Options $options): mixed
    {
        return 'mixed';
    }
}
