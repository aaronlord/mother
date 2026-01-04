<?php

declare(strict_types=1);

namespace Tests\Feature\Generator;

use Error;
use Lord\Mother\Attributes\MotherUsing;
use Lord\Mother\Contracts\GeneratorInterface;
use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Generator\Generator;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Container;
use Lord\Mother\Support\Options;
use Lord\Mother\Tests\Stubs\JobData;
use Lord\Mother\Tests\Stubs\PersonData;

mutates(Generator::class);

describe('generate', function () {
    it('generates an object with constructed properties', function () {
        class ConstructedDataStub
        {
            public function __construct(
                public string $value,
            ) {
            }
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(ConstructedDataStub::class, [], new Options());

        assert($result instanceof ConstructedDataStub);

        expect($result->value)->toBeString();
    });

    it('generates an object from public properties', function () {
        class PropertyDataStub
        {
            public string $value;
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(PropertyDataStub::class, [], new Options());

        assert($result instanceof PropertyDataStub);

        expect($result->value)->toBeString();
    });

    it('applies overrides', function () {
        class OverrideBDataStub
        {
            public string $valueC;

            public int $valueD;
        }

        class OverrideADataStub
        {
            public string $valueA;

            public int $valueB;

            public OverrideBDataStub $nested;
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(
            OverrideADataStub::class,
            [
                'valueA' => 'a',
                'nested' => [
                    'valueD' => 48,
                ],
            ],
            new Options()
        );

        assert($result instanceof OverrideADataStub);

        expect($result->valueA)->toBe('a');
        expect($result->valueB)->toBeInt();
        expect($result->nested)->toBeInstanceOf(OverrideBDataStub::class);
        expect($result->nested->valueC)->toBeString();
        expect($result->nested->valueD)->toBe(48);
    });

    it('applies overrides to default values', function () {
        class OverrideDefaultDataStub
        {
            public function __construct(
                public string $value = 'default value',
            ) {
            }
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(
            OverrideDefaultDataStub::class,
            [
                'value' => 'overridden value',
            ],
            new Options(),
        );

        assert($result instanceof OverrideDefaultDataStub);

        expect($result->value)->toBe('overridden value');
    });

    it('can skip populating nulls', function () {
        class SkipNullableDataStub
        {
            public ?string $value;
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(
            SkipNullableDataStub::class,
            [],
            new Options(populateNulls: false),
        );

        assert($result instanceof SkipNullableDataStub);

        expect($result->value)->toBeNull();
    });

    it('can populate nulls', function () {
        class PopulateNullableDataStub
        {
            public ?string $value;
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(
            PopulateNullableDataStub::class,
            [],
            new Options(populateNulls: true),
        );

        assert($result instanceof PopulateNullableDataStub);

        expect($result->value)->toBeString();
    });

    it('respects default values', function () {
        class RespectDefaultDataStub
        {
            public string $valueA = 'default value';

            public ?string $valueB = 'null default';
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(
            RespectDefaultDataStub::class,
            [],
            new Options(
                respectDefaultValues: true,
            ),
        );

        assert($result instanceof RespectDefaultDataStub);

        expect($result->valueA)->toBe('default value');
        expect($result->valueB)->toBeNull();
    });

    it('respects default values when populating nulls', function () {
        class RespectDefaultWithNullsDataStub
        {
            public string $valueA = 'default value';

            public ?string $valueB = 'null default';
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $result = $sut->generate(
            RespectDefaultWithNullsDataStub::class,
            [],
            new Options(
                respectDefaultValues: true,
                populateNulls: true,
            ),
        );

        assert($result instanceof RespectDefaultWithNullsDataStub);

        expect($result->valueA)->toBe('default value');
        expect($result->valueB)->toBe('null default');
    });

    it('respects max depth option', function () {
        class DepthDataStub
        {
            public function __construct(
                public string $value,
                public ?DepthDataStub $child,
            ) {
            }
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $options = new Options(
            maxDepth: 2,
            populateNulls: true,
        );

        $result = $sut->generate(DepthDataStub::class, [], $options);

        assert($result instanceof DepthDataStub);

        expect($result->value)->toBeString();
        expect($result->child)->toBeInstanceOf(DepthDataStub::class);

        assert($result->child instanceof DepthDataStub);

        expect($result->child->value)->toBeString();
        expect($result->child->child)->toBeNull();
    });

    it('executes callable overrides', function () {
        class CallableOverrideDataStub
        {
            public string $value;
        }

        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $person = $sut->generate(
            CallableOverrideDataStub::class,
            [
                'value' => static fn () => 'foo',
            ],
            new Options()
        );

        assert($person instanceof CallableOverrideDataStub);

        expect($person->value)->toBe('foo');
    });

    it('uses MotherUsing attribute generator over registry', function () {
        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        class StringGenerator implements ValueGeneratorInterface
        {
            public function supports(PropertyDefinition $property, Options $options): bool
            {
                return true;
            }

            public function generate(PropertyDefinition $property, Options $options): mixed
            {
                return 'string';
            }
        }

        class AttributeOverrideStub
        {
            #[MotherUsing(new StringGenerator())]
            public string $value;
        }

        $result = $sut->generate(AttributeOverrideStub::class, [], new Options());

        assert($result instanceof AttributeOverrideStub);

        expect($result->value)->toBe('string');
    });

    it('recursively generates class overrides', function () {
        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        $person = $sut->generate(
            PersonData::class,
            [
                'job' => [
                    'company' => 'Override Corp',
                ],
            ],
            new Options()
        );

        assert($person instanceof PersonData);

        expect($person->job)->toBeInstanceOf(JobData::class);
        expect($person->job->company)->toBe('Override Corp');
    });

    it('skips assigning non-nullable properties when value is null and populateNulls is false', function () {
        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        class NullReturningGenerator implements ValueGeneratorInterface
        {
            public function supports(PropertyDefinition $property, Options $options): bool
            {
                return true;
            }

            public function generate(PropertyDefinition $property, Options $options): mixed
            {
                return null;
            }
        }

        class NonNullablePropertyStub
        {
            #[MotherUsing(new NullReturningGenerator())]
            public string $value; // non-nullable
        }

        $result = $sut->generate(
            NonNullablePropertyStub::class,
            [],
            new Options(populateNulls: false),
        );

        assert($result instanceof NonNullablePropertyStub);

        // The 'value' property will exist ...
        expect($result)->toHaveProperty('value');
        // ...but accessing it will throw an error
        expect(fn () => $result->value)->toThrow(
            Error::class,
            'Typed property '.NonNullablePropertyStub::class.'::$value must not be accessed before initialization'
        );
    });

    it('ignores the supports result when using MotherUsing attribute', function () {
        /** @var GeneratorInterface $sut */
        $sut = Container::make()->get(Generator::class);

        class UnsupportedGenerator implements ValueGeneratorInterface
        {
            public function supports(PropertyDefinition $property, Options $options): bool
            {
                return false;
            }

            public function generate(PropertyDefinition $property, Options $options): mixed
            {
                return 'generated value';
            }
        }

        class AttributeWithUnsupportedGeneratorStub
        {
            #[MotherUsing(new UnsupportedGenerator())]
            public string $value;
        }

        $result = $sut->generate(AttributeWithUnsupportedGeneratorStub::class, [], new Options());

        assert($result instanceof AttributeWithUnsupportedGeneratorStub);

        expect($result->value)->toBe('generated value');
    });
});
