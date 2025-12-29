<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

/**
 * @template T of object
 */
interface BuilderInterface
{
    /**
     * Override a property with a given value. Nested properties are specified using dot notation (e.g. 'address.city').
     */
    public function with(string $property, mixed $value): static;

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
