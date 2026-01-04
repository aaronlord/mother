<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Support\Options;

interface GeneratorInterface
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<int|string, mixed> $overrides
     * @param Options $options
     * @return T|null
     */
    public function generate(string $class, array $overrides, Options $options): ?object;
}
