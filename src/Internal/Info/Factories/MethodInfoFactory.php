<?php

declare(strict_types=1);

namespace Pathway\Internal\Info\Factories;

use Pathway\Internal\Info\ParameterInfo;
use Pathway\Internal\Info\MethodInfo;

use ReflectionParameter;
use ReflectionMethod;

use function array_map;

/**
 * @internal
 */
final class MethodInfoFactory
{
    public static function make(string $class, string $method): MethodInfo
    {
        $reflectionMethod = ReflectionMethod::createFromMethodName($class . '::' . $method);

        $parameterInfos = array_map(
            static fn(ReflectionParameter $parameter): ParameterInfo => ParameterInfoFactory::make($parameter),
            $reflectionMethod->getParameters(),
        );

        return new MethodInfo($reflectionMethod, $parameterInfos);
    }

    private function __construct() {}
}
