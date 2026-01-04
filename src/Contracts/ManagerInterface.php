<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Support\Options;

interface ManagerInterface
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $overrides
     * @param Options|array<string, mixed> $options
     * @return T
     */
    public function make(
        string $class,
        array $overrides = [],
        Options|array $options = [],
    ): object;

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return BuilderInterface<T>
     */
    public function for(string $class): BuilderInterface;
}
