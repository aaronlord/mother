<?php

declare(strict_types=1);

namespace Tests\Unit\Generator;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Generator\ValueGeneratorRegistry;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;
use Mockery;
use RuntimeException;

mutates(ValueGeneratorRegistry::class);

describe('all', function () {
    it('returns all generators in the registry', function () {
        $generatorAMock = Mockery::mock(ValueGeneratorInterface::class);
        $generatorBMock = Mockery::mock(ValueGeneratorInterface::class);

        $registry = new ValueGeneratorRegistry($generatorAMock, $generatorBMock);

        $generators = $registry->all();

        expect($generators)->toHaveCount(2);

        expect($generators[0])->toBe($generatorAMock);
        expect($generators[1])->toBe($generatorBMock);
    });
});

describe('unsift', function () {
    it('adds generators to the beginning of the registry', function () {
        $generatorAMock = Mockery::mock(ValueGeneratorInterface::class);
        $generatorBMock = Mockery::mock(ValueGeneratorInterface::class);

        $registry = new ValueGeneratorRegistry($generatorAMock);

        $registry->unshift($generatorBMock);

        $generators = $registry->all();

        expect($generators)->toHaveCount(2);

        expect($generators[0])->toBe($generatorBMock);
        expect($generators[1])->toBe($generatorAMock);
    });
});

describe('push', function () {
    it('adds generators to the end of the registry', function () {
        $generatorAMock = Mockery::mock(ValueGeneratorInterface::class);
        $generatorBMock = Mockery::mock(ValueGeneratorInterface::class);

        $registry = new ValueGeneratorRegistry($generatorAMock);

        $registry->push($generatorBMock);

        $generators = $registry->all();

        expect($generators)->toHaveCount(2);

        expect($generators[0])->toBe($generatorAMock);
        expect($generators[1])->toBe($generatorBMock);
    });
});

describe('generate', function () {
    it('uses the first generator that supports the property to generate a value', function () {
        $propertyMock = Mockery::mock(PropertyDefinition::class);

        $optionsMock = Mockery::mock(Options::class);

        $generatorA = Mockery::mock(ValueGeneratorInterface::class);
        $generatorA->shouldReceive('supports')
            ->with($propertyMock, $optionsMock)
            ->andReturn(false);

        $generatorB = Mockery::mock(ValueGeneratorInterface::class);
        $generatorB
            ->shouldReceive('supports')
            ->with($propertyMock, $optionsMock)
            ->andReturn(true);
        $generatorB
            ->shouldReceive('generate')
            ->with($propertyMock, $optionsMock)
            ->andReturn('generated-value');

        $registry = new ValueGeneratorRegistry($generatorA, $generatorB);

        $value = $registry->generate($propertyMock, $optionsMock);

        expect($value)->toBe('generated-value');
    });

    it('throws an exception if no generator supports the property', function () {
        $property = Mockery::mock(PropertyDefinition::class);
        $property->name = 'testProperty';

        $options = Mockery::mock(Options::class);

        $generatorA = Mockery::mock(ValueGeneratorInterface::class);
        $generatorA
            ->shouldReceive('supports')
            ->with($property, $options)
            ->andReturn(false);

        $registry = new ValueGeneratorRegistry($generatorA);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No generator for testProperty');

        $registry->generate($property, $options);
    });
});
