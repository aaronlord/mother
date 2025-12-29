<?php

declare(strict_types=1);

namespace Lord\Mother\Example\Generators;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Generator\ValueGenerators\IntGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

readonly class IdGenerator extends IntGenerator implements ValueGeneratorInterface
{
    public function __construct()
    {
        parent::__construct(min: 1);
    }

    public function supports(PropertyDefinition $property, Options $options): bool
    {
        if ($property->type === null) {
            return false;
        }

        return $property->type === 'int' && $property->name === 'id';
    }
}
