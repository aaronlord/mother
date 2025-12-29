<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use Lord\Mother\Generator\ValueGenerators\EnumGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;
use Lord\Mother\Tests\Stubs\SuitEnum;
use RuntimeException;
use stdClass;

mutates(EnumGenerator::class);

describe('supports', function () {
    it('should return true for enum type', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: SuitEnum::class,
        );

        $options = new Options();

        $sut = new EnumGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    });

    it('should return false', function (?string $type) {
        $property = new PropertyDefinition(
            name: 'value',
            type: $type,
        );

        $options = new Options();

        $sut = new EnumGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    })->with([
        'for null type' => [null],
        'for non-string type' => ['string'],
        'for non-enum class type' => [stdClass::class],
    ]);
});

describe('generate', function () {
    it('should select a random enum case', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: SuitEnum::class,
        );

        $options = new Options();

        $sut = new EnumGenerator();

        $result = $sut->generate($property, $options);

        expect($result)->toBeInstanceOf(SuitEnum::class);
    });

    it('should respect only filter', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: SuitEnum::class,
        );

        $options = new Options();

        $sut = new EnumGenerator(
            only: [SuitEnum::HEARTS],
        );

        $result = $sut->generate($property, $options);

        expect($result)->toBe(SuitEnum::HEARTS);
    });

    it('should respect except filter', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: SuitEnum::class,
        );

        $options = new Options();

        $sut = new EnumGenerator(
            except: [SuitEnum::DIAMONDS, SuitEnum::CLUBS, SuitEnum::SPADES],
        );

        $result = $sut->generate($property, $options);

        expect($result)->toBe(SuitEnum::HEARTS);
    });

    it('returns null if no cases are available and populateNulls is true', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: SuitEnum::class,
            nullable: true,
        );

        $options = new Options(
            populateNulls: true,
        );

        $sut = new EnumGenerator(
            only: [SuitEnum::HEARTS],
            except: [SuitEnum::HEARTS],
        );

        $result = $sut->generate($property, $options);

        expect($result)->toBeNull();
    });

    it('throws an exception if no cases are available and populateNulls is false', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: SuitEnum::class,
        );

        $options = new Options(
            populateNulls: false,
        );

        $sut = new EnumGenerator(
            only: [SuitEnum::HEARTS],
            except: [SuitEnum::HEARTS],
        );

        $sut->generate($property, $options);
    })->throws(RuntimeException::class, 'No enum cases available after applying filters.');
});
