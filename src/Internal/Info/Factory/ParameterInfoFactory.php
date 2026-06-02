<?php
declare(strict_types=1);

namespace Pathway\Internal\Info\Factory;

use Pathway\Internal\Info\ParameterInfo;

use ReflectionParameter;

/**
 * @internal
 */
class ParameterInfoFactory
{
    public function __construct(private readonly TypeInfoFactory $typeInfoFactory)
    {
    }

    public function make(ReflectionParameter $parameter): ParameterInfo
    {
        $typeInfo = $this->typeInfoFactory->make($parameter->getType());

        return new ParameterInfo($parameter, $typeInfo);
    }
}
