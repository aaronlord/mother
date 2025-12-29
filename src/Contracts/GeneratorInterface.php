<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Support\Options;

/**
 * @template T of object
 */
interface GeneratorInterface
{
    /**
     * @param class-string<T> $class
     * @param array<int|string, mixed> $overrides
     * @param Options $options
     * @return T|null
     */
    public function generate(string $class, array $overrides, Options $options): ?object;
}
