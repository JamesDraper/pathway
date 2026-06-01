<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

use ReflectionParameter;

/**
 * @internal
 */
class ParameterInfo
{
    public function __construct(
        private readonly ReflectionParameter $parameter,
        private readonly TypeInfo $typeInfo
    ) {
    }

    public function getName(): string
    {
        return $this->parameter->getName();
    }

    public function isVariadic(): bool
    {
        return $this->parameter->isVariadic();
    }

    public function hasDefault(): bool
    {
        return $this->parameter->isDefaultValueAvailable();
    }

    public function getDefault(): mixed
    {
        return $this->hasDefault()
            ? $this->parameter->getDefaultValue()
            : null;
    }

    public function getTypeInfo(): TypeInfo
    {
        return $this->typeInfo;
    }
}
