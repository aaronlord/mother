<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

interface ObjectGeneratorInterface extends ValueGeneratorInterface
{
    public function generate(PropertyDefinition $property, Options $options): ?object;
}
