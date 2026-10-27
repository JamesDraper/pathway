<?php

declare(strict_types=1);

namespace Tests;

use ReflectionClass;

use function interface_exists;
use function is_subclass_of;
use function class_exists;
use function sprintf;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Creates an instance without calling its constructor, for filling a required argument.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    final protected function makeDummy(string $class): object
    {
        return new ReflectionClass($class)->newInstanceWithoutConstructor();
    }

    /**
     * @param class-string $class
     * @param class-string $interface
     */
    final protected function assertImplementsInterface(string $class, string $interface): void
    {
        $this->assertClassExists($class);
        $this->assertInterfaceExists($interface);

        $message = sprintf('Class "%s" does not implement interface "%s".', $class, $interface);

        $this->assertTrue(is_subclass_of($class, $interface), $message);
    }

    /**
     * @param class-string $interface
     * @param class-string $parent
     */
    final protected function assertExtendsInterface(string $interface, string $parent): void
    {
        $this->assertInterfaceExists($interface);
        $this->assertInterfaceExists($parent);

        $message = sprintf('Interface "%s" does not extend interface "%s".', $interface, $parent);

        $this->assertTrue(is_subclass_of($interface, $parent), $message);
    }

    /**
     * @param class-string $interface
     */
    final protected function assertInterfaceExists(string $interface): void
    {
        $message = sprintf('Interface "%s" does not exist.', $interface);

        $this->assertTrue(interface_exists($interface), $message);
    }

    /**
     * @param class-string $class
     */
    final protected function assertClassExists(string $class): void
    {
        $message = sprintf('Class "%s" does not exist.', $class);

        $this->assertTrue(class_exists($class), $message);
    }
}
