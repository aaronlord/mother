<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

use Lord\Mother\Support\Options;

interface BuilderFactoryInterface
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @return BuilderInterface<T>
     */
    public function make(string $class, Options $options = new Options()): BuilderInterface;
}
