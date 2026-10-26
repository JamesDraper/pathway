<?php

declare(strict_types=1);

namespace Pathway\Internal\Info\Factories;

use Pathway\Internal\Info\TypeInfo;

use ReflectionType;

/**
 * @internal
 */
final class TypeInfoFactory
{
    public static function make(?ReflectionType $type): TypeInfo
    {
        return new TypeInfo($type);
    }

    private function __construct() {}
}
