<?php

declare(strict_types=1);

namespace Lord\Mother;

use Closure;
use Lord\Mother\Contracts\BuilderInterface;
use Lord\Mother\Contracts\ManagerInterface;
use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Generator\ValueGeneratorRegistry;
use Lord\Mother\Support\Container;
use Psr\Container\ContainerInterface;

class Mother
{
    protected static ?ContainerInterface $container = null;

    /**
     * @var ?Closure(): ContainerInterface
     */
    protected static ?Closure $containerResolver = null;

    /**
     * Create an instance of the given class.
     *
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $overrides
     * @param array<string, mixed> $options
     * @return T
     */
    public static function make(
        string $class,
        array $overrides = [],
        array $options = [],
    ): object {
        /** @var ManagerInterface<T> */
        $manager = self::resolve(ManagerInterface::class);

        return $manager->make($class, $overrides, $options);
    }

    /**
     * Create a builder for the given class.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return BuilderInterface<T>
     */
    public static function for(string $class): BuilderInterface
    {
        /** @var ManagerInterface<T> */
        $manager = self::resolve(ManagerInterface::class);

        return $manager->for($class);
    }

    /**
     * Register a custom value generator.
     */
    public static function register(ValueGeneratorInterface $generator): void
    {
        self::resolve(ValueGeneratorRegistry::class)
            ->unshift($generator);
    }

    /**
     * @param Closure(): ContainerInterface $resolver
     */
    public static function resolveContainerUsing(callable $resolver): void
    {
        self::$containerResolver = $resolver;
        self::$container = null;
    }

    /**
     * @template R of object
     * @param class-string<R> $class
     * @return R
     */
    protected static function resolve(string $class): object
    {
        /** @var R */
        return self::container()
            ->get($class);
    }

    protected static function container(): ContainerInterface
    {
        return self::$container
            ??= (self::$containerResolver
                ? (self::$containerResolver)()
                : Container::make());
    }
}
