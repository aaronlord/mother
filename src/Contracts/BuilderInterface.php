<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

/**
 * @template T of object
 */
interface BuilderInterface
{
    /**
     * Override properties, or a property with the given values. Nested properties are specified using dot notation (e.g. 'address.city').
     *
     * @param array<int|string, mixed>|int|string $property
     */
    public function with(array|int|string $property, mixed $value = null): static;

    /**
     * Populate properties with null values instead of leaving them unset.
     */
    public function populateNulls(): static;

    /**
     * @param positive-int $count Number of instances to create
     * @return ($count is 1 ? T : list<T>)
     */
    public function make(int $count = 1): object|array;
}
