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

    public function make(
        string $class,
        array $overrides = [],
        Options|array $options = [],
    ): object {
        if (is_array($options)) {
            $options = Options::from($options);
        }

        return $this->builderFactory
            ->make($class, $options)
            ->with($overrides)
            ->make(1);
    }

    public function for(string $class): BuilderInterface
    {
        return $this->builderFactory->make($class);
    }
}
