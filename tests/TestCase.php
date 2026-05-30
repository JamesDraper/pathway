<?php
declare(strict_types=1);

namespace Tests;

use ReflectionClass;

use function interface_exists;
use function is_subclass_of;
use function class_exists;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param class-string $interface
     */
    final protected function assertInterfaceExists(string $interface): void
    {
        $this->assertTrue(interface_exists($interface));
    }

    /**
     * @param class-string $class
     */
    final protected function assertClassExists(string $class): void
    {
        $this->assertTrue(class_exists($class));
    }

    /**
     * @param class-string $class
     */
    final protected function assertFinal(string $class): void
    {
        $reflectionClass = new ReflectionClass($class);
        
        $this->assertTrue($reflectionClass->isFinal());
    }

    /**
     * @param class-string $child
     * @param class-string $parent
     */
    final protected function assertChildOf(string $child, string $parent): void
    {
        $this->assertTrue(is_subclass_of($child, $parent));
    }
}
