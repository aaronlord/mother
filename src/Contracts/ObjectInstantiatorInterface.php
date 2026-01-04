<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

interface ObjectInstantiatorInterface
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     * @return T
     */
    public function create(string $class, array $args): object;
}
