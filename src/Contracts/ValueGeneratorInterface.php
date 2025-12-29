<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

interface ValueGeneratorInterface
{
    public function supports(PropertyDefinition $property, Options $options): bool;

    public function generate(PropertyDefinition $property, Options $options): mixed;
}
