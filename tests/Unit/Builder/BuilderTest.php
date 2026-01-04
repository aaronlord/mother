<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Builder;

use Lord\Mother\Builder\Builder;
use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Contracts\GeneratorInterface;
use Lord\Mother\Contracts\OverrideExpanderInterface;
use Lord\Mother\Mother;
use Lord\Mother\Support\Options;
use Lord\Mother\Tests\Stubs\PersonData;
use Mockery;
use RuntimeException;

mutates(Builder::class);

describe('di', function () {
    it('is an instance of BuilderInterface', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $options = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $options);

        expect($builder)->toBeInstanceOf(BuilderInterface::class);
    });
});

describe('with', function () {
    it('sets a single value', function (int|string $property) {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturnUsing(static fn (string $class, array $overrides) => (object) ['overrides' => $overrides]);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $optionMock = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $optionMock);

        $builder->with($property, 'value');

        /** @var object{overrides: array<int|string, mixed>} $object */
        $object = $builder->make();

        expect($object->overrides)->toHaveKey($property, 'value');
    })->with([
        'a string key' => [
            'property' => 'property',
        ],
        'an integer key' => [
            'property' => 0,
        ],
    ]);

    it('overwrites existing values', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturnUsing(static fn (string $class, array $overrides) => (object) ['overrides' => $overrides]);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $optionMock = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $optionMock);

        $builder->with('property', 'initial value');
        $builder->with('property', 'new value');

        /** @var object{overrides: array<int|string, mixed>} $object */
        $object = $builder->make();

        expect($object->overrides)->toHaveKey('property', 'new value');
    });

    it('allows chaining', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturnUsing(static fn (string $class, array $overrides) => (object) ['overrides' => $overrides]);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $optionMock = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $optionMock);

        $builder
            ->with('firstProperty', 'first value')
            ->with('secondProperty', 'second value');

        /** @var object{overrides: array<int|string, mixed>} $object */
        $object = $builder->make();

        expect($object->overrides)
            ->toHaveKey('firstProperty', 'first value')
            ->toHaveKey('secondProperty', 'second value');
    });
});

describe('populateNulls', function () {
    it('sets the populateNulls option to true', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturnUsing(static fn (string $class, array $overrides, Options $options) => (object) ['options' => $options]);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $options = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $options);

        $builder->populateNulls();

        /** @var object{options: Options} $object */
        $object = $builder->make();

        expect($object->options->populateNulls)->toBeTrue();
    });

    it('has a default value of false', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturnUsing(static fn (string $class, array $overrides, Options $options) => (object) ['options' => $options]);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $options = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $options);

        /** @var object{options: Options} $object */
        $object = $builder->make();

        expect($object->options->populateNulls)->toBeFalse();
    });

    it('allows chaining', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturnUsing(static fn (string $class, array $overrides, Options $options) => (object) ['options' => $options]);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $options = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $options);

        $builder
            ->with('property', 'value')
            ->populateNulls();

        /** @var object{options: Options} $object */
        $object = $builder->make();

        expect($object->options->populateNulls)->toBeTrue();
    });
});

describe('make', function () {
    it('generates a single instance when count is 1', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn(Mother::make(PersonData::class));

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $options = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $options);

        $result = $builder->make(1);

        expect($result)->toBeInstanceOf(PersonData::class);
    });

    it('generates multiple instances when count is greater than 1', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->times(3)
            ->andReturn(Mother::make(PersonData::class));

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $options = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $options);

        $result = $builder->make(3);

        expect($result)
            ->toBeArray()
            ->toHaveCount(3)
            ->toContainOnlyInstancesOf(PersonData::class);
    });
});

describe('generate', function () {
    it('throws an exception when the generator returns null', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $generatorMock
            ->shouldReceive('generate')
            ->once()
            ->andReturnNull();

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $overrideExpanderMock
            ->shouldReceive('expand')
            ->once()
            ->andReturnArg(0);

        $options = new Options();

        $builder = new Builder(PersonData::class, $generatorMock, $overrideExpanderMock, $options);

        $builder->make();
    })->throws(RuntimeException::class, 'Could not generate an instance of '.PersonData::class);
});
