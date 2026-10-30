<?php

declare(strict_types=1);

namespace Pathway\Info;

use function array_key_exists;
use function class_exists;

final class ClassInfoFactory
{
    /**
     * @var array<string, ClassInfo|null>
     */
    private static array $classInfos = [];

    public static function make(string $class): ?ClassInfo
    {
        if (!array_key_exists($class, self::$classInfos)) {
            self::$classInfos[$class] = class_exists($class)
                ? new ClassInfo($class)
                : null;
        }

        return self::$classInfos[$class];
    }

    private function __construct() {}
}
