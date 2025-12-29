<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use Lord\Mother\Generator\ValueGenerators\NullGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

mutates(NullGenerator::class);

describe('supports', function () {
    it('should return true for null type', function () {
        $property = new PropertyDefinition(
            name: 'values',
            type: 'null',
        );

        $options = new Options();

        $sut = new NullGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false for non-null type', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'string',
        );

        $options = new Options();

        $sut = new NullGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate an empty null', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'null',
        );

        $options = new Options();

        $sut = new NullGenerator();

        $result = $sut->generate($property, $options);

        expect($result)->toBeNull();
    });
});
