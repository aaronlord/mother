<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Lord\Mother\Support\Container;
use Psr\Container\ContainerInterface;
use stdClass;

mutates(Container::class);

describe('make', function () {
    it('creates a container instance', function () {
        $sut = Container::make();

        expect($sut)
            ->toBeInstanceOf(Container::class)
            ->toBeInstanceOf(ContainerInterface::class);
    });
});

describe('get', function () {
    it('retrieves an instance from the container', function () {
        $object = new stdClass();

        $sut = new Container([
            'mock' => $object,
        ]);

        $result = $sut->get('mock');

        expect($result)->toBe($object);
    });

    it('throws an exception when the instance is not found', function () {
        //
    })->skip('Not implemented yet');
});

describe('has', function () {
    it('returns true when the instance exists in the container', function () {
        $object = new stdClass();

        $sut = new Container([
            'mock' => $object,
        ]);

        $result = $sut->has('mock');

        expect($result)->toBeTrue();
    });

    it('returns false when the instance does not exist in the container', function () {
        $sut = new Container([]);

        $result = $sut->has('nonexistent');

        expect($result)->toBeFalse();
    });
});
