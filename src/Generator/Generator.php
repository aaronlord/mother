<?php

declare(strict_types=1);

namespace Lord\Mother\Generator;

use Lord\Mother\Attributes\MotherUsing;
use Lord\Mother\Contracts\GeneratorInterface;
use Lord\Mother\Contracts\ObjectGeneratorInterface;
use Lord\Mother\Contracts\ObjectInstantiatorInterface;
use Lord\Mother\Contracts\PropertyResolverInterface;
use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Contracts\ValueGeneratorRegistryInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;
use ReflectionClass;

/**
 * @template T of object
 */
final class Generator implements GeneratorInterface
{
    public function __construct(
        protected readonly PropertyResolverInterface $resolver,
        protected readonly ObjectInstantiatorInterface $instantiator,
        protected readonly ValueGeneratorRegistryInterface $registry,
    ) {
    }

    /**
     * @param class-string<T> $class
     * @return T|null
     */
    public function generate(string $class, array $overrides, Options $options): ?object
    {
        $generator = $this->generatorFromClass($class);

        if ($generator instanceof ObjectGeneratorInterface) {
            $object = $generator->generate(
                new PropertyDefinition(
                    name: '__class__',
                    type: $class,
                    nullable: false,
                    hasDefault: false,
                    default: null,
                    attributes: [],
                ),
                $options
            );

            assert($object instanceof $class || $object === null);

            return $object;
        }

        if ($options->depth($class) >= $options->maxDepth) {
            return null;
        }

        $options = $options->enter($class);
        $definitions = $this->resolver->for($class);

        $data = [];

        foreach ($definitions as $property) {
            $value = $this->resolvePropertyValue($property, $overrides, $options);

            if ($this->shouldSkipProperty($property, $value, $options)) {
                continue;
            }

            $data[$property->name] = $value;
        }

        return $this->instantiator->create($class, $data);
    }

    /**
     * @param class-string<T> $class
     */
    protected function generatorFromClass(string $class): ?ObjectGeneratorInterface
    {
        $reflection = new ReflectionClass($class);
        $attrs = $reflection->getAttributes(MotherUsing::class);

        if ($attrs === []) {
            return null;
        }

        /** @var MotherUsing $attribute */
        $attribute = $attrs[0]->newInstance();

        assert(
            $attribute->class instanceof ObjectGeneratorInterface,
            sprintf(
                'Attribute %s on class %s must be an instance of %s',
                MotherUsing::class,
                $class,
                ObjectGeneratorInterface::class,
            )
        );

        return $attribute->class;
    }

    /**
     * @param array<int|string, mixed> $overrides
     */
    protected function resolvePropertyValue(
        PropertyDefinition $property,
        array $overrides,
        Options $options,
    ): mixed {
        if (array_key_exists($property->name, $overrides)) {
            return $this->resolveOverride($property, $overrides[$property->name], $options);
        }

        if ($generator = $this->generatorFromAttribute($property)) {
            return $generator->generate($property, $options);
        }

        if ($property->nullable && ! $options->populateNulls) {
            return null;
        }

        if ($property->hasDefault && $options->respectDefaultValues) {
            return $property->default;
        }

        if (
            is_string($property->type)
            && class_exists($property->type)
            && ! enum_exists($property->type)
        ) {
            /** @var class-string<T> $type */
            $type = $property->type;

            return $this->generate($type, [], $options);
        }

        return $this->registry->generate($property, $options);
    }

    protected function resolveOverride(
        PropertyDefinition $property,
        mixed $override,
        Options $options,
    ): mixed {
        if (is_callable($override)) {
            return $override();
        }

        if (
            is_string($property->type)
            && ! $override instanceof $property->type
            && class_exists($property->type)
            && ! enum_exists($property->type)
        ) {
            /** @var class-string<T> $type */
            $type = $property->type;

            return $this->generate(
                $type,
                is_array($override) ? $override : [],
                $options
            );
        }

        return $override;
    }

    protected function generatorFromAttribute(PropertyDefinition $property): ?ValueGeneratorInterface
    {
        $attr = $property->attributes[MotherUsing::class] ?? null;

        return $attr instanceof MotherUsing
            ? $attr->class
            : null;
    }

    protected function shouldSkipProperty(
        PropertyDefinition $property,
        mixed $value,
        Options $options,
    ): bool {
        return $value === null
            && ! $property->nullable
            && ! $options->populateNulls;
    }
}
