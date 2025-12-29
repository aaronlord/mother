<?php

declare(strict_types=1);

namespace Lord\Mother\Generator\ValueGenerators;

use InvalidArgumentException;
use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

readonly class FloatGenerator implements ValueGeneratorInterface
{
    public function __construct(
        public float $min = -1_000_000.0,
        public float $max = 1_000_000.0,
        public int $scale = 100_000, // precision: 0.00001
    ) {
        if ($this->scale < 1) {
            throw new InvalidArgumentException('Scale must be >= 1');
        }

        if ($this->min > $this->max) {
            throw new InvalidArgumentException('Min cannot be greater than max');
        }
    }

    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->type === 'float';
    }

    public function generate(PropertyDefinition $property, Options $options): float
    {
        $normalised = mt_rand(0, $this->scale) / $this->scale;

        return $this->min + ($normalised * ($this->max - $this->min));
    }
}
