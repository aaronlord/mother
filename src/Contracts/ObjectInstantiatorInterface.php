<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

/**
 * @template T of object
 */
interface ObjectInstantiatorInterface
{
    /**
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     * @return T
     */
    public function create(string $class, array $args): object;
}
