<?php

declare(strict_types=1);

namespace Lord\Mother\Example\Generators;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Example\ValueObjects\NameValue;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

class NameGenerator implements ValueGeneratorInterface
{
    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->type === NameValue::class;
    }

    public function generate(PropertyDefinition $property, Options $options): mixed
    {
        return new NameValue(
            forename: ['John', 'Jane'][rand(0, 1)],
            middlename: null,
            surname: 'Doe',
        );
    }
}
