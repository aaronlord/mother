<?php

declare(strict_types=1);

namespace Lord\Mother;

use Lord\Mother\Contracts\BuilderFactoryInterface;
use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Contracts\ManagerInterface;
use Lord\Mother\Support\Options;

/**
 * @template T of object
 *
 * @implements ManagerInterface<T>
 */
class Manager implements ManagerInterface
{
    /**
     * @param BuilderFactoryInterface<T> $builderFactory
     */
    public function __construct(
        protected BuilderFactoryInterface $builderFactory,
    ) {
    }

    /**
     * @param class-string<T> $class
     * @param array<string, mixed> $overrides
     * @param array<string, mixed> $options
     * @return T
     */
    public function make(
        string $class,
        array $overrides = [],
        array $options = [],
    ): object {
        $builder = $this->builderFactory->make($class, Options::from($options));

        foreach ($overrides as $prop => $value) {
            $builder = $builder->with($prop, $value);
        }

        return $builder->make(1);
    }

    /**
     * @param class-string<T> $class
     * @return BuilderInterface<T>
     */
    public function for(string $class): BuilderInterface
    {
        return $this->builderFactory->make($class);
    }
}
