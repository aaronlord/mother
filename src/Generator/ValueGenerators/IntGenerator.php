<?php

declare(strict_types=1);

namespace Lord\Mother\Generator\ValueGenerators;

use InvalidArgumentException;
use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

readonly class IntGenerator implements ValueGeneratorInterface
{
    public function __construct(
        public int $min = -1_000_000,
        public int $max = 1_000_000,
    ) {
        if ($min > $max) {
            throw new InvalidArgumentException('Min cannot be greater than max.');
        }
    }

    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->type === 'int';
    }

    public function generate(PropertyDefinition $property, Options $options): int
    {
        return mt_rand($this->min, $this->max);
    }
}
