<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use InvalidArgumentException;
use Lord\Mother\Generator\ValueGenerators\IntGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

mutates(IntGenerator::class);

describe('supports', function () {
    it('should return true for int type', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'int',
        );

        $options = new Options();

        $sut = new IntGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false for non-int type', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'string',
        );

        $options = new Options();

        $sut = new IntGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate a random int', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'int',
        );

        $options = new Options();

        $sut = new IntGenerator();

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeInt()
            ->toBeGreaterThanOrEqual(PHP_INT_MIN)
            ->toBeLessThanOrEqual(PHP_INT_MAX);
    });

    it('should generate sufficiently random ints', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'int',
        );

        $options = new Options();

        $sut = new IntGenerator();

        $results = [];

        for ($i = 0; $i < 100; $i++) {
            $results[] = $sut->generate($property, $options);
        }

        $uniqueResults = array_unique($results);

        expect(count($uniqueResults))->toBeGreaterThan(90);
    });

    it('should respect min and max bounds', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'int',
        );

        $options = new Options();

        $sut = new IntGenerator(min: 10, max: 20);

        for ($i = 0; $i < 100; $i++) {
            $result = $sut->generate($property, $options);

            expect($result)
                ->toBeInt()
                ->toBeGreaterThanOrEqual(10)
                ->toBeLessThanOrEqual(20);
        }
    });

    it('should handle edge case where min equals max', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'int',
        );

        $options = new Options();

        $sut = new IntGenerator(min: 42, max: 42);

        $result = $sut->generate($property, $options);

        expect($result)->toBeInt()->toBe(42);
    });

    it('should handle negative bounds correctly', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'int',
        );

        $options = new Options();

        $sut = new IntGenerator(min: -100, max: -50);

        for ($i = 0; $i < 100; $i++) {
            $result = $sut->generate($property, $options);

            expect($result)
                ->toBeInt()
                ->toBeGreaterThanOrEqual(-100)
                ->toBeLessThanOrEqual(-50);
        }
    });

    it('throws exception if min is greater than max', function () {
        new IntGenerator(
            min: 1,
            max: 0,
        );
    })->throws(InvalidArgumentException::class, 'Min cannot be greater than max.');
});
