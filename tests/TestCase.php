<?php
declare(strict_types=1);

namespace Tests;

use function interface_exists;
use function is_subclass_of;

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
     * @param class-string $child
     * @param class-string $parent
     */
    final protected function assertChildOf(string $child, string $parent): void
    {
        $this->assertTrue(is_subclass_of($child, $parent));
    }
}
