<?php
declare(strict_types=1);

namespace Tests;

use DG\BypassFinals;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Mockery;

use ReflectionClass;

use function interface_exists;
use function is_subclass_of;
use function class_exists;
use function file_exists;
use function mkdir;

abstract class TestCase extends MockeryTestCase
{
    private const BYPASS_FINALS_CACHE_DIR = __DIR__ . '/../.bypass-finals-cache/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->bypassFinals();
    }

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

    private function bypassFinals(): void
    {
        if (!file_exists(self::BYPASS_FINALS_CACHE_DIR)) {
            mkdir(self::BYPASS_FINALS_CACHE_DIR, 0755);
        }

        BypassFinals::denyPaths(['*/vendor/*']);

        BypassFinals::setCacheDirectory(self::BYPASS_FINALS_CACHE_DIR);

        BypassFinals::enable(
            bypassReadOnly: false,
            bypassFinal: true,
        );
    }
}
