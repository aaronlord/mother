<?php

declare(strict_types=1);

namespace Lord\Mother\Generator\ValueGenerators;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;
use RuntimeException;

readonly class EnumGenerator implements ValueGeneratorInterface
{
    /**
     * @param list<mixed> $only
     * @param list<mixed> $except
     */
    public function __construct(
        public array $only = [],
        public array $except = [],
    ) {
    }

    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return is_string($property->type) && enum_exists($property->type);
    }

    public function generate(PropertyDefinition $property, Options $options): mixed
    {
        assert(is_string($property->type) && enum_exists($property->type));

        $cases = $property->type::cases();

        if (! empty($this->only)) {
            $cases = array_filter($cases, fn (mixed $case): bool => in_array($case, $this->only, true));
        }

        if (! empty($this->except)) {
            $cases = array_filter($cases, fn (mixed $case): bool => ! in_array($case, $this->except, true));
        }

        if (empty($cases)) {
            if ($property->nullable && $options->populateNulls) {
                return null;
            }

            throw new RuntimeException('No enum cases available after applying filters.');
        }

        return $cases[array_rand($cases)];
    }
}
