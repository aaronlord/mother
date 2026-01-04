<?php

declare(strict_types=1);

namespace Tests\Unit\Reflection;

use Lord\Mother\Reflection\ObjectInstantiator;
use RuntimeException;

mutates(ObjectInstantiator::class);

describe('create', function () {
    it('creates an instance of a class with a constructor', function () {
        class ConstructorStub
        {
            public function __construct(
                public string $name,
            ) {
            }
        }

        $sut = new ObjectInstantiator();

        $result = $sut->create(ConstructorStub::class, ['name' => 'test']);

        expect($result)->toBeInstanceOf(ConstructorStub::class);
        expect($result->name)->toBe('test');
    });

    it('creates an instance of a class without a constructor', function () {
        class NoConstructorStub
        {
            public string $name = '';
        }

        $sut = new ObjectInstantiator();

        $result = $sut->create(NoConstructorStub::class, ['name' => 'test']);

        expect($result)->toBeInstanceOf(NoConstructorStub::class);
        expect($result->name)->toBe('test');
    });

    it('creates an instance of a class with default constructor parameters', function () {
        class DefaultConstructorStub
        {
            public function __construct(
                public string $name = 'default',
            ) {
            }
        }

        $sut = new ObjectInstantiator();

        $result = $sut->create(DefaultConstructorStub::class, []);

        expect($result)->toBeInstanceOf(DefaultConstructorStub::class);
        expect($result->name)->toBe('default');
    });

    it('throws an exception when required constructor parameters are missing', function () {
        class RequiredConstructorStub
        {
            public function __construct(
                public string $name,
            ) {
            }
        }

        $sut = new ObjectInstantiator();

        $sut->create(RequiredConstructorStub::class, []);
    })->throws(RuntimeException::class, 'Missing required constructor parameter "name" for class "'.RequiredConstructorStub::class.'"');

    it('throws an exception when trying to create an enum', function () {
        enum SampleEnum: string
        {
            case OPTION_A = 'A';
            case OPTION_B = 'B';
        }

        $sut = new ObjectInstantiator();

        $sut->create(SampleEnum::class, []);
    })->throws(RuntimeException::class, 'Enums should be handled by EnumGenerator, not ObjectInstantiator');

    it('does not set properties that do not exist in the class', function () {
        class ExtraPropertyStub
        {
            public string $name = '';
        }

        $sut = new ObjectInstantiator();

        $result = $sut->create(ExtraPropertyStub::class, [
            'name' => 'test',
            'age' => 30,
        ]);

        expect($result)->toBeInstanceOf(ExtraPropertyStub::class);
        expect($result->name)->toBe('test');
        expect(property_exists($result, 'age'))->toBeFalse();
    })->throws(RuntimeException::class, 'Property "age" does not exist on class "'.ExtraPropertyStub::class.'"');
});
