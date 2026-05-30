<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

use ReflectionClass;

use function class_exists;

/**
 * @internal
 */
final class ClassInfoFactory
{
    public function make(string $class): ?ClassInfo
    {
        if (!class_exists($class)) {
            return null;
        }

        return new ClassInfo(
            new ReflectionClass($class),
        );
    }
}
