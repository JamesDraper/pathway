<?php
declare(strict_types=1);

namespace Pathway\Internal\Info\Factory;

use Pathway\Internal\Info\ParameterInfo;
use Pathway\Internal\Info\MethodInfo;

use ReflectionMethod;

use function array_map;

class MethodInfoFactory
{
    public function __construct(private readonly ParameterInfoFactory $parameterInfoFactory)
    {
    }

    public function make(ReflectionMethod $method): MethodInfo
    {
        $parameterInfos = array_map(
            fn (ReflectionParameter $parameter): ParameterInfo => $this->parameterInfoFactory->make($parameter),
            $method->getParameters(),
        );

        return new MethodInfo($method, $parameterInfos);
    }
}
