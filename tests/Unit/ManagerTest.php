<?php

declare(strict_types=1);

namespace Tests\Unit;

use Lord\Mother\Contracts\BuilderFactoryInterface;
use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Manager;
use Lord\Mother\Support\Options;
use Mockery;

mutates(Manager::class);

describe('make', function () {
    it('creates an instance using the builder factory', function () {
        class MakeStub
        {
        }

        $builderMock = Mockery::mock(BuilderInterface::class);

        $builderMock
            ->shouldReceive('with')
            ->once()
            ->with([])
            ->andReturnSelf();

        $builderMock
            ->shouldReceive('make')
            ->once()
            ->with(1)
            ->andReturn(new MakeStub());

        $builderFactoryMock = Mockery::mock(BuilderFactoryInterface::class);

        $builderFactoryMock
            ->shouldReceive('make')
            ->once()
            ->with(MakeStub::class, Mockery::type(Options::class))
            ->andReturn($builderMock);

        $sut = new Manager($builderFactoryMock);

        $result = $sut->make(MakeStub::class);

        expect($result)->toBeInstanceOf(MakeStub::class);
    });

    it('applies overrides to the builder before making the instance', function () {
        class OverrideStub
        {
            public function __construct(
                public string $name,
            ) {
            }
        }

        $builderMock = Mockery::mock(BuilderInterface::class);

        $builderMock
            ->shouldReceive('with')
            ->once()
            ->with(['name' => 'test'])
            ->andReturnSelf();

        $builderMock
            ->shouldReceive('make')
            ->once()
            ->with(1)
            ->andReturn(new OverrideStub('test'));

        $builderFactoryMock = Mockery::mock(BuilderFactoryInterface::class);

        $builderFactoryMock
            ->shouldReceive('make')
            ->once()
            ->with(OverrideStub::class, Mockery::type(Options::class))
            ->andReturn($builderMock);

        $sut = new Manager($builderFactoryMock);

        $result = $sut->make(OverrideStub::class, ['name' => 'test']);

        expect($result)->toBeInstanceOf(OverrideStub::class);
        expect($result->name)->toBe('test');
    });

    it('passes options to the builder factory', function () {
        class OptionsStub
        {
        }

        $options = [
            'maxDepth' => 0,
        ];

        $builderMock = Mockery::mock(BuilderInterface::class);

        $builderMock
            ->shouldReceive('with')
            ->once()
            ->with([])
            ->andReturnSelf();

        $builderMock
            ->shouldReceive('make')
            ->once()
            ->with(1)
            ->andReturn(new OptionsStub());

        $builderFactoryMock = Mockery::mock(BuilderFactoryInterface::class);

        $builderFactoryMock
            ->shouldReceive('make')
            ->once()
            ->with(
                OptionsStub::class,
                Mockery::on(static fn (Options $opts) => $opts->maxDepth === 0)
            )
            ->andReturn($builderMock);

        $sut = new Manager($builderFactoryMock);

        $result = $sut->make(OptionsStub::class, [], $options);

        expect($result)->toBeInstanceOf(OptionsStub::class);
    });
});

describe('for', function () {
    it('returns a builder for the specified class', function () {
        class ForStub
        {
        }

        $builderMock = Mockery::mock(BuilderInterface::class);

        $builderFactoryMock = Mockery::mock(BuilderFactoryInterface::class);

        $builderFactoryMock
            ->shouldReceive('make')
            ->once()
            ->with(ForStub::class)
            ->andReturn($builderMock);

        $sut = new Manager($builderFactoryMock);

        $result = $sut->for(ForStub::class);

        expect($result)->toBe($builderMock);
    });
});
