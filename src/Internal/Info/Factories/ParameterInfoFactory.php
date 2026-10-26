<?php

declare(strict_types=1);

namespace Pathway\Internal\Info\Factories;

use Pathway\Internal\Info\ParameterInfo;

use ReflectionParameter;

/**
 * @internal
 */
final class ParameterInfoFactory
{
    public static function make(ReflectionParameter $parameter): ParameterInfo
    {
        $typeInfo = TypeInfoFactory::make($parameter->getType());

        return new ParameterInfo($parameter, $typeInfo);
    }

    private function __construct();
}
