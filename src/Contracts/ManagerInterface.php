<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

/**
 * @template T of object
 */
interface ManagerInterface
{
    /**
     * @param class-string<T> $class
     * @param array<string, mixed> $overrides
     * @param array<string, mixed> $options
     * @return T
     */
    public function make(string $class, array $overrides = [], array $options = []): object;

    /**
     * @param class-string<T> $class
     * @return BuilderInterface<T>
     */
    public function for(string $class): BuilderInterface;
}
