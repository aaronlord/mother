<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use Lord\Mother\Generator\ValueGenerators\MixedGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

mutates(MixedGenerator::class);

describe('supports', function () {
    it('should return true for mixed type', function () {
        $property = new PropertyDefinition(
            name: 'values',
            type: 'mixed',
        );

        $options = new Options();

        $sut = new MixedGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false for non-mixed type', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'string',
        );

        $options = new Options();

        $sut = new MixedGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate an empty mixed', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'mixed',
        );

        $options = new Options();

        $sut = new MixedGenerator();

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeString()
            ->toBe('mixed');
    });
});
