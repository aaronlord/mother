<?php

declare(strict_types=1);

namespace Lord\Mother\Reflection;

use Lord\Mother\Contracts\PropertyResolverInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;

/**
 * @template T of object
 */
class PropertyResolver implements PropertyResolverInterface
{
    /**
     * @var array<string, list<PropertyDefinition>>
     */
    protected array $cache = [];

    /**
     * @param class-string<T> $class
     */
    public function for(string $class): array
    {
        if (isset($this->cache[$class])) {
            return $this->cache[$class];
        }

        if (! class_exists($class)) {
            throw new RuntimeException("Class {$class} does not exist.");
        }

        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        $definitions = $constructor instanceof ReflectionMethod
            ? $this->resolveConstructorParameters($constructor)
            : $this->resolvePublicProperties($reflection);

        return $this->cache[$class] = $definitions;
    }

    /**
     * @param ReflectionMethod $constructor
     * @return list<PropertyDefinition>
     */
    protected function resolveConstructorParameters(ReflectionMethod $constructor): array
    {
        $definitions = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $this->getType($param);
            $isNullable = $this->isNullable($type);
            $hasDefault = $param->isDefaultValueAvailable();
            $default = $hasDefault ? $param->getDefaultValue() : null;

            $definitions[] = new PropertyDefinition(
                name: $param->getName(),
                type: $type?->getName(),
                nullable: $isNullable,
                hasDefault: $hasDefault,
                default: $default,
                attributes: $this->getAttributes($param)
            );
        }

        return $definitions;
    }

    /**
     * @param ReflectionClass<T> $reflection
     * @return list<PropertyDefinition>
     */
    protected function resolvePublicProperties(ReflectionClass $reflection): array
    {
        $definitions = [];

        foreach ($reflection->getProperties() as $prop) {
            if (! $prop->isPublic()) {
                continue;
            }

            $type = $this->getType($prop);
            $isNullable = $this->isNullable($type);
            $hasDefault = $prop->hasDefaultValue();
            $default = $hasDefault ? $prop->getDefaultValue() : null;

            $definitions[] = new PropertyDefinition(
                name: $prop->getName(),
                type: $type?->getName(),
                nullable: $isNullable,
                hasDefault: $hasDefault,
                default: $default,
                attributes: $this->getAttributes($prop)
            );
        }

        return $definitions;
    }

    /**
     * TODO: Handle intersection types
     * TODO: Handle 'self', 'parent', and 'static' types
     */
    protected function getType(ReflectionParameter|ReflectionProperty $param): ?ReflectionNamedType
    {
        $type = $param->getType();

        if ($type instanceof ReflectionNamedType) {
            return $type;
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof ReflectionNamedType) {
                    return $unionType;
                }
            }
        }

        return null;
    }

    protected function isNullable(?ReflectionNamedType $type): bool
    {
        if ($type === null) {
            return true;
        }

        return $type->allowsNull();
    }

    /**
     * @return array<string, object>
     */
    protected function getAttributes(ReflectionParameter|ReflectionProperty $reflection): array
    {
        $attributes = [];

        foreach ($reflection->getAttributes() as $attr) {
            $attributes[$attr->getName()] = $attr->newInstance();
        }

        return $attributes;
    }
}
