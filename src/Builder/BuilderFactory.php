<?php

declare(strict_types=1);

namespace Lord\Mother\Builder;

use Lord\Mother\Contracts\BuilderFactoryInterface;
use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Contracts\GeneratorInterface;
use Lord\Mother\Contracts\OverrideExpanderInterface;
use Lord\Mother\Support\Options;

/**
 * @template T of object
 *
 * @implements BuilderFactoryInterface<T>
 */
class BuilderFactory implements BuilderFactoryInterface
{
    /**
     * @param GeneratorInterface<T> $generator
     */
    public function __construct(
        protected GeneratorInterface $generator,
        protected OverrideExpanderInterface $overrideExpander,
    ) {
    }

    /**
     * @param class-string<T> $class
     * @return BuilderInterface<T>
     */
    public function make(string $class, Options $options = new Options()): BuilderInterface
    {
        return new Builder($class, $this->generator, $this->overrideExpander, $options);
    }
}
