<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use Lord\Mother\Generator\ValueGenerators\StringGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;
use RuntimeException;

mutates(StringGenerator::class);

describe('supports', function () {
    it('should return true for string type', function () {
        $property = new PropertyDefinition(
            name: 'example',
            type: 'string',
        );

        $options = new Options();

        $sut = new StringGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false for non-string type', function () {
        $property = new PropertyDefinition(
            name: 'example',
            type: 'bool',
        );

        $options = new Options();

        $sut = new StringGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate a random string', function () {
        $property = new PropertyDefinition(
            name: 'username',
            type: 'string',
        );

        $options = new Options();

        $sut = new StringGenerator();

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeString()
            ->toMatch('/^[a-zA-Z0-9]{3,20}$/');
    });

    it('should generate sufficiently random strings', function () {
        $property = new PropertyDefinition(
            name: 'token',
            type: 'string',
        );

        $options = new Options();

        $sut = new StringGenerator();

        $results = [];

        for ($i = 0; $i < 100; $i++) {
            $results[] = $sut->generate($property, $options);
        }

        $uniqueResults = array_unique($results);

        expect(count($uniqueResults))->toBeGreaterThan(90);
    });

    it('should generate a string with prefix', function () {
        $property = new PropertyDefinition(
            name: 'username',
            type: 'string',
        );

        $options = new Options();

        $sut = new StringGenerator(
            prefix: 'user_',
            minLength: 8,
            maxLength: 20,
        );

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeString()
            ->toMatch('/^user_[a-zA-Z0-9]{3,17}$/'); // 3 to 17 because prefix is 5 characters
    });

    it('should generate a string with custom length', function () {
        $property = new PropertyDefinition(
            name: 'username',
            type: 'string',
        );

        $options = new Options();

        $sut = new StringGenerator(minLength: 5, maxLength: 10);

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeString()
            ->toMatch('/^[a-zA-Z0-9]{5,10}$/');
    });

    it('should generate a string with custom dictionary', function () {
        $property = new PropertyDefinition(
            name: 'code',
            type: 'string',
        );

        $options = new Options();

        $sut = new StringGenerator(dictionary: 'ABCDEF');

        $result = $sut->generate($property, $options);

        expect($result)
            ->toBeString()
            ->toMatch('/^[ABCDEF]{3,20}$/');
    });

    it('throws an exception if prefix is too long for minimum length', function () {
        $property = new PropertyDefinition(
            name: 'username',
            type: 'string',
        );

        $options = new Options();

        $sut = new StringGenerator(prefix: 'prefix', minLength: 1, maxLength: 5);

        $sut->generate($property, $options);
    })->throws(RuntimeException::class, 'Prefix is too long for the minimum length.');
});
