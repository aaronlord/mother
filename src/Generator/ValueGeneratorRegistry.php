<?php

declare(strict_types=1);

namespace Lord\Mother\Generator;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Contracts\ValueGeneratorRegistryInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;
use RuntimeException;

class ValueGeneratorRegistry implements ValueGeneratorRegistryInterface
{
    /**
     * @var list<ValueGeneratorInterface>
     */
    protected array $generators = [];

    public function __construct(ValueGeneratorInterface ...$generator)
    {
        $this->push(...$generator);
    }

    public function all(): array
    {
        return $this->generators;
    }

    public function unshift(ValueGeneratorInterface ...$generator): self
    {
        array_unshift($this->generators, ...$generator);

        return $this;
    }

    public function push(ValueGeneratorInterface ...$generator): self
    {
        array_push($this->generators, ...$generator);

        return $this;
    }

    public function generate(PropertyDefinition $property, Options $options): mixed
    {
        foreach ($this->generators as $generator) {
            if (! $generator->supports($property, $options)) {
                continue;
            }

            return $generator->generate($property, $options);
        }

        throw new RuntimeException("No generator for {$property->name}");
    }
}
