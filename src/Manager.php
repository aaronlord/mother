<?php

declare(strict_types=1);

namespace Lord\Mother;

use Lord\Mother\Contracts\BuilderFactoryInterface;
use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Contracts\ManagerInterface;
use Lord\Mother\Support\Options;

class Manager implements ManagerInterface
{
    public function __construct(
        protected BuilderFactoryInterface $builderFactory,
    ) {
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function make(
        string $class,
        array $overrides = [],
        Options|array $options = [],
    ): object {
        if (is_array($options)) {
            $options = Options::from($options);
        }

        $builder = $this->builderFactory->make($class, $options);

        foreach ($overrides as $prop => $value) {
            $builder = $builder->with($prop, $value);
        }

        return $builder->make(1);
    }

    public function for(string $class): BuilderInterface
    {
        return $this->builderFactory->make($class);
    }
}
