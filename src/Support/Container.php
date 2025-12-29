<?php

declare(strict_types=1);

namespace Lord\Mother\Support;

use Lord\Mother\Builder\BuilderFactory;
use Lord\Mother\Builder\OverrideExpander;
use Lord\Mother\Contracts\GeneratorInterface;
use Lord\Mother\Contracts\ManagerInterface;
use Lord\Mother\Contracts\ObjectInstantiatorInterface;
use Lord\Mother\Contracts\PropertyResolverInterface;
use Lord\Mother\Contracts\ValueGeneratorRegistryInterface;
use Lord\Mother\Generator\Generator;
use Lord\Mother\Generator\ValueGeneratorRegistry;
use Lord\Mother\Generator\ValueGenerators\ArrayGenerator;
use Lord\Mother\Generator\ValueGenerators\BoolGenerator;
use Lord\Mother\Generator\ValueGenerators\DateTimeGenerator;
use Lord\Mother\Generator\ValueGenerators\EnumGenerator;
use Lord\Mother\Generator\ValueGenerators\FloatGenerator;
use Lord\Mother\Generator\ValueGenerators\IntGenerator;
use Lord\Mother\Generator\ValueGenerators\MixedGenerator;
use Lord\Mother\Generator\ValueGenerators\StringGenerator;
use Lord\Mother\Manager;
use Lord\Mother\Reflection\ObjectInstantiator;
use Lord\Mother\Reflection\PropertyResolver;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @param array<string, object> $instances
     */
    public function __construct(
        protected array $instances,
    ) {
    }

    public static function make(): ContainerInterface
    {
        $valueGeneratorRegistry = new ValueGeneratorRegistry(
            new EnumGenerator(),
            new ArrayGenerator(),
            new StringGenerator(),
            new IntGenerator(),
            new FloatGenerator(),
            new BoolGenerator(),
            new DateTimeGenerator(),
            new MixedGenerator(),
        );

        $propertyResolver = new PropertyResolver();

        $objectInstantiator = new ObjectInstantiator();

        $generator = new Generator(
            $propertyResolver,
            $objectInstantiator,
            $valueGeneratorRegistry,
        );

        $overrideExpander = new OverrideExpander();

        $builderFactory = new BuilderFactory($generator, $overrideExpander);

        $manager = new Manager($builderFactory);

        return new self([
            Generator::class => $generator,
            GeneratorInterface::class => $generator,
            Manager::class => $manager,
            ManagerInterface::class => $manager,
            ObjectInstantiator::class => $objectInstantiator,
            ObjectInstantiatorInterface::class => $objectInstantiator,
            OverrideExpander::class => $overrideExpander,
            PropertyResolver::class => $propertyResolver,
            PropertyResolverInterface::class => $propertyResolver,
            ValueGeneratorRegistry::class => $valueGeneratorRegistry,
            ValueGeneratorRegistryInterface::class => $valueGeneratorRegistry,
        ]);
    }

    public function get(string $id): mixed
    {
        // TODO: Throw exception if not found

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }
}
