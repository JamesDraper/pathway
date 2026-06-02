<?php
declare(strict_types=1);

namespace Tests;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Mockery;

use function interface_exists;
use function is_subclass_of;
use function class_exists;

abstract class TestCase extends MockeryTestCase
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T&MockInterface
     */
    final protected function makeMock(string $class): MockInterface
    {
        return Mockery::mock($class);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param list<mixed> $constructorArguments
     * @return T&MockInterface
     */
    final protected function makePartialMock(string $class, array $constructorArguments = []): MockInterface
    {
        $mock = Mockery::mock($class, $constructorArguments)->makePartial();

        $mock->shouldAllowMockingProtectedMethods();

        return $mock;
    }

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
     * @param class-string $child
     * @param class-string $parent
     */
    final protected function assertChildOf(string $child, string $parent): void
    {
        $this->assertTrue(is_subclass_of($child, $parent));
    }
}
