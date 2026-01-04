<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Unit\Generator\ValueGenerators;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Lord\Mother\Generator\ValueGenerators\DateTimeGenerator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

mutates(DateTimeGenerator::class);

describe('supports', function () {
    it('should return true for DateTime types', function (string $type) {
        $property = new PropertyDefinition(
            name: 'value',
            type: $type,
        );

        $options = new Options();

        $sut = new DateTimeGenerator();

        expect($sut->supports($property, $options))->toBeTrue();
    })->with([
        'DateTime',
        'DateTimeImmutable',
        'DateTimeInterface',
    ]);

    it('should return false for non-DateTime type', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'string',
        );

        $options = new Options();

        $sut = new DateTimeGenerator();

        expect($sut->supports($property, $options))->toBeFalse();
    });
});

describe('generate', function () {
    it('should generate a random DateTimeImmutable', function (string $type) {
        $property = new PropertyDefinition(
            name: 'value',
            type: $type,
        );

        $options = new Options();

        $sut = new DateTimeGenerator();

        $result = $sut->generate($property, $options);

        expect($result)->toBeInstanceOf(DateTimeImmutable::class);
    })->with([
        'DateTimeImmutable',
        'DateTimeInterface',
    ]);

    it('should generate a random DateTime', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'DateTime',
        );

        $options = new Options();

        $sut = new DateTimeGenerator();

        $result = $sut->generate($property, $options);

        expect($result)->toBeInstanceOf(DateTime::class);
    });

    it('should generate sufficiently random DateTime objects', function () {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'DateTime',
        );

        $options = new Options();

        $sut = new DateTimeGenerator();

        $results = [];

        for ($i = 0; $i < 100; $i++) {
            $results[] = $sut->generate($property, $options)->format('Y-m-d H:i:s');
        }

        $uniqueResults = array_unique($results);

        expect(count($uniqueResults))->toBeGreaterThan(90);
    });

    it('should generate dates from', function (DateTimeInterface|int|string $from, DateTimeInterface|int|string $to) {
        $property = new PropertyDefinition(
            name: 'value',
            type: 'DateTime',
        );

        $options = new Options();

        $sut = new DateTimeGenerator(
            from: $from,
            to: $to,
        );

        $result = $sut->generate($property, $options);

        $fromTimestamp = is_string($from) ? strtotime($from) : ($from instanceof DateTimeInterface ? $from->getTimestamp() : $from);
        $toTimestamp = is_string($to) ? strtotime($to) : ($to instanceof DateTimeInterface ? $to->getTimestamp() : $to);

        assert(is_int($fromTimestamp));
        assert(is_int($toTimestamp));

        expect($result->getTimestamp())->toBeGreaterThanOrEqual($fromTimestamp);
        expect($result->getTimestamp())->toBeLessThanOrEqual($toTimestamp);
    })->with([
        'DateTime' => [new DateTime('2024-01-01'), new DateTime('2024-12-31')],
        'DateTimeImmutable' => [new DateTimeImmutable('2024-01-01'), new DateTimeImmutable('2024-12-31')],
        'Timestamps' => [1704067200, 1735689599],
        'Strings' => ['2024-01-01', '2024-12-31'],
    ]);

    it('throws exception if from is after to', function () {
        new DateTimeGenerator(
            from: 1,
            to: 0,
        );
    })->throws(InvalidArgumentException::class, 'The "from" date must be earlier than or equal to the "to" date.');
});
