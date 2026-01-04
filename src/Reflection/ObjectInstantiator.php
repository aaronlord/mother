<?php

declare(strict_types=1);

namespace Lord\Mother\Reflection;

use Lord\Mother\Contracts\ObjectInstantiatorInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;

class ObjectInstantiator implements ObjectInstantiatorInterface
{
    public function create(string $class, array $args): object
    {
        $reflection = new ReflectionClass($class);

        if ($reflection->isEnum()) {
            throw new RuntimeException('Enums should be handled by EnumGenerator, not ObjectInstantiator');
        }

        $constructor = $reflection->getConstructor();

        return $constructor !== null
            ? $this->newInstanceFromConstructor($reflection, $constructor, $args)
            : $this->newInstanceWithoutConstructor($reflection, $args);
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflection
     * @param array<string, mixed> $args
     * @return T
     */
    protected function newInstanceFromConstructor(
        ReflectionClass $reflection,
        ReflectionMethod $constructor,
        array $args,
    ): object {
        $args = $this->resolveConstructorArgs($constructor->getParameters(), $args, $reflection->getName());

        return $reflection->newInstanceArgs($args);
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflection
     * @param array<string, mixed> $args
     * @return T
     */
    protected function newInstanceWithoutConstructor(
        ReflectionClass $reflection,
        array $args,
    ): object {
        $instance = $reflection->newInstanceWithoutConstructor();

        $this->assignProperties($reflection, $instance, $args);

        return $instance;
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @param array<int|string, mixed> $data
     * @param string $class
     * @return list<mixed>
     */
    protected function resolveConstructorArgs(array $parameters, array $data, string $class): array
    {
        $args = [];

        foreach ($parameters as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $data)) {
                $args[] = $data[$name];

                continue;
            }

            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();

                continue;
            }

            throw new RuntimeException(sprintf(
                'Missing required constructor parameter "%s" for class "%s"',
                $name,
                $class
            ));
        }

        return $args;
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflection
     * @param object $instance
     * @param array<int|string, mixed> $data
     */
    protected function assignProperties(ReflectionClass $reflection, object $instance, array $data): void
    {
        $properties = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $properties[$prop->getName()] = $prop;
        }

        foreach ($data as $key => $value) {
            if (! isset($properties[$key])) {
                throw new RuntimeException(sprintf(
                    'Property "%s" does not exist on class "%s"',
                    $key,
                    $reflection->getName()
                ));
            }

            $properties[$key]->setValue($instance, $value);
        }
    }
}
