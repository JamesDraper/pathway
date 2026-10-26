<?php

declare(strict_types=1);

namespace Pathway\Internal\Info\Factories;

use Pathway\Internal\Info\ClassInfo;

use function class_exists;

/**
 * @internal
 */
final class ClassInfoFactory
{
    public static function make(string $class): ?ClassInfo
    {
        if (!class_exists($class)) {
            return null;
        }

        return new ClassInfo($class);
    }

    private function __construct();
}
