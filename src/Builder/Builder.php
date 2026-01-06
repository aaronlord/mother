<?php

declare(strict_types=1);

namespace Lord\Mother\Builder;

use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Contracts\GeneratorInterface;
use Lord\Mother\Contracts\OverrideExpanderInterface;
use Lord\Mother\Support\Options;
use RuntimeException;

/**
 * @template T of object
 *
 * @implements BuilderInterface<T>
 */
class Builder implements BuilderInterface
{
    /**
     * @var array<int|string, mixed>
     */
    protected array $overrides = [];

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        protected readonly string $class,
        protected readonly GeneratorInterface $generator,
        protected readonly OverrideExpanderInterface $overrideExpander,
        protected Options $options,
    ) {
    }

    public function with(array|int|string $property, mixed $value = null): static
    {
        if (is_array($property)) {
            foreach ($property as $key => $value) {
                $this->overrides[$key] = $value;
            }
        } else {
            $this->overrides[$property] = $value;
        }

        return $this;
    }

    public function populateNulls(): static
    {
        $this->options->populateNulls = true;

        return $this;
    }

    public function make(int $count = 1): object|array
    {
        $this->overrides = $this->overrideExpander->expand($this->overrides);

        if ($count === 1) {
            return $this->generate();
        }

        $results = [];

        for ($i = 0; $i < $count; $i++) {
            $results[] = $this->generate();
        }

        return $results;
    }

    /**
     * @return T
     */
    protected function generate(): object
    {
        $object = $this->generator->generate($this->class, $this->overrides, $this->options);

        if ($object === null) {
            throw new RuntimeException("Could not generate an instance of {$this->class}");
        }

        return $object;
    }
}
