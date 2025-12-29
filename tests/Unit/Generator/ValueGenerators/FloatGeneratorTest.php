<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use InvalidArgumentException;
use Lord\Mother\Generator\ValueGenerators\FloatGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

mutates(FloatGenerator::class);

describe('supports', function () {
    it('should return true for float type', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'float',
        );

        $options = new Options();

        $sut = new FloatGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false for non-float type', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'string',
        );

        $options = new Options();

        $sut = new FloatGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate a random float', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'float',
        );

        $options = new Options();

        $sut = new FloatGenerator();

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeFloat()
            ->toBeGreaterThanOrEqual(-1_000_000.0)
            ->toBeLessThanOrEqual(1_000_000.0);
    });

    it('should generate sufficiently random floats', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'float',
        );

        $options = new Options();

        $sut = new FloatGenerator();

        $results = [];

        for ($i = 0; $i < 100; $i++) {
            $results[] = $sut->generate($property, $options);
        }

        $uniqueResults = array_unique($results);

        expect(count($uniqueResults))->toBeGreaterThan(90);
    });

    it('should respect min and max', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'float',
        );

        $options = new Options();

        $sut = new FloatGenerator(min: 10.5, max: 20.5);

        for ($i = 0; $i < 100; $i++) {
            $result = $sut->generate($property, $options);

            expect($result)
                ->toBeGreaterThanOrEqual(10.5)
                ->toBeLessThanOrEqual(20.5);
        }
    });

    it('should respect scale for precision', function () {
        $property = new PropertyDefinition(
            name: 'number',
            type: 'float',
        );

        $options = new Options();

        $sut = new FloatGenerator(min: 0.0, max: 1.0, scale: 1000);

        for ($i = 0; $i < 100; $i++) {
            $result = $sut->generate($property, $options);

            $decimalPart = explode('.', (string)$result)[1] ?? '0';

            expect(strlen($decimalPart))->toBeLessThanOrEqual(3);
        }
    });

    it('should throw exception if min is greater than max', function () {
        new FloatGenerator(min: 20.0, max: 10.0);
    })->throws(InvalidArgumentException::class, 'Min cannot be greater than max');

    it('should throw exception if scale is less than 1', function () {
        new FloatGenerator(min: 0.0, max: 1.0, scale: 0);
    })->throws(InvalidArgumentException::class, 'Scale must be >= 1');
});
