<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use Lord\Mother\Generator\ValueGenerators\BoolGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

mutates(BoolGenerator::class);

describe('supports', function () {
    it('should return true for bool type', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'bool',
        );

        $options = new Options();

        $sut = new BoolGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false for non-bool type', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'string',
        );

        $options = new Options();

        $sut = new BoolGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate a random bool', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'bool',
        );

        $options = new Options();

        $sut = new BoolGenerator();

        $result = $sut->generate($property, $options);

        expect($result)->toBeBool();
    });

    it('should generate sufficiently random bools', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'bool',
        );

        $options = new Options();

        $sut = new BoolGenerator();

        $results = [];

        for ($i = 0; $i < 100; $i++) {
            $results[] = $sut->generate($property, $options);
        }

        expect(in_array(true, $results, true))->toBeTrue();
        expect(in_array(false, $results, true))->toBeTrue();
    });
});
