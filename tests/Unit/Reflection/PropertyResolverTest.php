<?php

declare(strict_types=1);

namespace Tests\Unit\Reflection;

use DateTime;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Reflection\PropertyResolver;
use RuntimeException;

mutates(PropertyResolver::class);

describe('for', function () {
    it('resolves properties from constructor parameters', function () {
        class ConstructorResolverStub
        {
            public function __construct(
                public string $name,
            ) {
            }
        }

        $sut = new PropertyResolver();

        $result = $sut->for(ConstructorResolverStub::class);

        expect($result)
            ->toBeArray()
            ->toHaveLength(1)
            ->toContainOnlyInstancesOf(PropertyDefinition::class);

        $definition = $result[0];

        expect($definition->name)->toBe('name');
        expect($definition->type)->toBe('string');
        expect($definition->attributes)->toBeEmpty();
        expect($definition->nullable)->toBeFalse();
        expect($definition->hasDefault)->toBeFalse();
        expect($definition->default)->toBeNull();
    });

    it('resolves properties from public class properties', function () {
        class PublicPropertyResolverStub
        {
            public string $name;
        }

        $sut = new PropertyResolver();

        $result = $sut->for(PublicPropertyResolverStub::class);

        expect($result)
            ->toBeArray()
            ->toHaveLength(1)
            ->toContainOnlyInstancesOf(PropertyDefinition::class);

        $definition = $result[0];

        expect($definition->name)->toBe('name');
        expect($definition->type)->toBe('string');
        expect($definition->attributes)->toBeEmpty();
        expect($definition->nullable)->toBeFalse();
        expect($definition->hasDefault)->toBeFalse();
        expect($definition->default)->toBeNull();
    });

    it('does not resolve non-public properties', function () {
        class NonPublicPropertyResolverStub
        {
            protected string $name;

            private int $age; // @phpstan-ignore property.unused
        }

        $sut = new PropertyResolver();

        $result = $sut->for(NonPublicPropertyResolverStub::class);

        expect($result)
            ->toBeArray()
            ->toHaveLength(0);
    });

    it('uses the first union type', function () {
        class UnionTypeResolverStub
        {
            public function __construct(
                public string|int $identifier,
            ) {
            }
        }

        $sut = new PropertyResolver();

        $result = $sut->for(UnionTypeResolverStub::class);

        expect($result)
            ->toBeArray()
            ->toHaveLength(1)
            ->toContainOnlyInstancesOf(PropertyDefinition::class);

        $definition = $result[0];

        expect($definition->name)->toBe('identifier');
        expect($definition->type)->toBe('string');
        expect($definition->attributes)->toBeEmpty();
        expect($definition->nullable)->toBeFalse();
        expect($definition->hasDefault)->toBeFalse();
        expect($definition->default)->toBeNull();
    });

    it('handles non-typed properties', function () {
        class NonTypedResolverStub
        {
            public function __construct(// @phpstan-ignore missingType.parameter
                public $data,
            ) {
            }
        }

        $sut = new PropertyResolver();

        $result = $sut->for(NonTypedResolverStub::class);

        expect($result)
            ->toBeArray()
            ->toHaveLength(1)
            ->toContainOnlyInstancesOf(PropertyDefinition::class);

        $definition = $result[0];

        expect($definition->name)->toBe('data');
        expect($definition->type)->toBeNull();
        expect($definition->attributes)->toBeEmpty();
        expect($definition->nullable)->toBeTrue();
        expect($definition->hasDefault)->toBeFalse();
        expect($definition->default)->toBeNull();
    });

    it('returns cached definitions on subsequent calls', function () {
        $sut = new PropertyResolver();

        $resultA = $sut->for(DateTime::class);
        $resultB = $sut->for(DateTime::class);

        expect($resultA)->toBe($resultB);
    });

    it('throws an exception for non-existent classes', function () {
        $sut = new PropertyResolver();

        $sut->for('NonExistentClass'); // @phpstan-ignore argument.type
    })->throws(RuntimeException::class, 'Class NonExistentClass does not exist.');
});
