<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use DateTime;
use Lord\Mother\Support\Options;
use stdClass;

mutates(Options::class);

describe('from', function () {
    it('creates an Options instance from an array', function () {
        $optionsArray = [
            'populateNulls' => true,
        ];

        $sut = Options::from($optionsArray);

        expect($sut->maxDepth)->toBe(3); // default value
        expect($sut->populateNulls)->toBeTrue();
        expect($sut->respectDefaultValues)->toBeTrue(); // default value
    });

    it('ignores invalid keys in the options array', function () {
        $optionsArray = [
            'invalid' => 'value',
        ];

        $sut = Options::from($optionsArray);

        expect(property_exists($sut, 'invalid'))->toBeFalse();
    });

    it('ignores non-string keys in the options array', function () {
        $optionsArray = [
            0 => 'foo',
        ];

        $sut = Options::from($optionsArray);

        expect(property_exists($sut, '0'))->toBeFalse();
    });
});

describe('depth & enter', function () {
    it('increments depth correctly for multiple classes', function () {
        $sut = new Options();

        $sutA = $sut->enter(stdClass::class);
        $sutB = $sutA->enter(DateTime::class);
        $sutC = $sutB->enter(stdClass::class);

        expect($sutA->depth(stdClass::class))->toBe(1);
        expect($sutA->depth(DateTime::class))->toBe(0);

        expect($sutB->depth(stdClass::class))->toBe(1);
        expect($sutB->depth(DateTime::class))->toBe(1);

        expect($sutC->depth(stdClass::class))->toBe(2);
        expect($sutC->depth(DateTime::class))->toBe(1);
    });
});
