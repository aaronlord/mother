<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Feature;

use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Mother;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Container;
use Lord\Mother\Support\Options;
use Lord\Mother\Tests\Stubs\AddressData;
use Lord\Mother\Tests\Stubs\PersonData;

mutates(Mother::class);

describe('make', function () {
    it('creates an instance of the given class', function () {
        $person = Mother::make(PersonData::class);

        expect($person)->toBeInstanceOf(PersonData::class);
        expect($person->name)->toBeString();
        expect($person->age)->toBeInt();
        expect($person->address)->toBeInstanceOf(AddressData::class);
    });

    it('allows overriding properties', function () {
        $person = Mother::make(PersonData::class, [
            'name' => 'John Doe',
            'age' => 30,
        ]);

        expect($person->name)->toBe('John Doe');
        expect($person->age)->toBe(30);
    });

    it('allows overriding nested properties with dot notation', function () {
        $person = Mother::make(PersonData::class, [
            'address.line1' => '123 Main St',
            'address.postcode' => '12345',
        ]);

        expect($person->address->line1)->toBe('123 Main St');
        expect($person->address->line2)->toBeNull();
        expect($person->address->line3)->toBeNull();
        expect($person->address->postcode)->toBe('12345');
    });
});

describe('for', function () {
    it('creates a builder for the given class', function () {
        $builder = Mother::for(PersonData::class);

        expect($builder)->toBeInstanceOf(BuilderInterface::class);
    });
});

describe('register', function () {
    it('registers a custom value generator', function () {
        class GeneratorStub implements ValueGeneratorInterface
        {
            public function supports(PropertyDefinition $property, Options $options): bool
            {
                return $property->name === 'name';
            }

            public function generate(PropertyDefinition $property, Options $options): mixed
            {
                return 'john doe';
            }
        }

        Mother::register(new GeneratorStub());

        $person = Mother::make(PersonData::class);

        expect($person->name)->toBe('john doe');
    });
});

describe('resolveContainerUsing', function () {
    it('resolves the container using the given resolver', function () {
        $called = false;

        Mother::resolveContainerUsing(function () use (&$called) {
            $called = true;

            return Container::make();
        });

        Mother::make(PersonData::class);

        expect($called)->toBeTrue();
    });
});
