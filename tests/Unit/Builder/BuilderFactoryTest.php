<?php

declare(strict_types=1);

namespace Tests\Unit\Builder;

use Lord\Mother\Builder\Builder;
use Lord\Mother\Builder\BuilderFactory;
use Lord\Mother\Contracts\GeneratorInterface;
use Lord\Mother\Contracts\OverrideExpanderInterface;
use Lord\Mother\Tests\Stubs\PersonData;
use Mockery;

mutates(BuilderFactory::class);

describe('make', function () {
    it('creates a Builder instance', function () {
        $generatorMock = Mockery::mock(GeneratorInterface::class);

        $overrideExpanderMock = Mockery::mock(OverrideExpanderInterface::class);

        $factory = new BuilderFactory($generatorMock, $overrideExpanderMock);

        $builder = $factory->make(PersonData::class);

        expect($builder)->toBeInstanceOf(Builder::class);
    });
});
