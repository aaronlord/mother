<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use Lord\Mother\Generator\ValueGenerators\ArrayGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

mutates(ArrayGenerator::class);

describe('supports', function () {
    it('should return true for array type', function () {
        $property = new PropertyDefinition(
            name: 'values',
            type: 'array',
        );

        $options = new Options();

        $sut = new ArrayGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false for non-array type', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'string',
        );

        $options = new Options();

        $sut = new ArrayGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate an empty array', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'array',
        );

        $options = new Options();

        $sut = new ArrayGenerator();

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeArray()
            ->toBeEmpty();
    });
});
