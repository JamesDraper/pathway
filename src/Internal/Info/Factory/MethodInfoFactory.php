<?php
declare(strict_types=1);

namespace Pathway\Internal\Info\Factory;

use Pathway\Internal\Info\ParameterInfo;
use Pathway\Internal\Info\MethodInfo;

use ReflectionParameter;
use ReflectionMethod;

use function array_map;

class MethodInfoFactory
{
    public function __construct(private readonly ParameterInfoFactory $parameterInfoFactory)
    {
    }

    public function make(string $class, string $method): MethodInfo
    {
        $reflectionMethod = ReflectionMethod::createFromMethodName($class . '::' . $method);

        $parameterInfos = array_map(
            fn (ReflectionParameter $parameter): ParameterInfo => $this->parameterInfoFactory->make($parameter),
            $reflectionMethod->getParameters(),
        );

        return new MethodInfo($reflectionMethod, $parameterInfos);
    }
}
