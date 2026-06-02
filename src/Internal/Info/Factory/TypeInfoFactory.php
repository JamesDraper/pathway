<?php
declare(strict_types=1);

namespace Pathway\Internal\Info\Factory;

use Pathway\Internal\Info\TypeInfo;

use ReflectionType;

/**
 * @internal
 */
class TypeInfoFactory
{
    public function make(?ReflectionType $type): TypeInfo
    {
        return new TypeInfo($type);
    }
}
