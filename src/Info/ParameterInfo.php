<?php

declare(strict_types=1);

namespace Pathway\Info;

use ReflectionParameter;

/**
 * @internal
 */
class ParameterInfo
{
    private ?TypeInfo $typeInfo = null;

    public function __construct(
        private readonly ReflectionParameter $parameter,
        private readonly MethodInfo $method,
    ) {}

    public function getMethodInfo(): MethodInfo
    {
        return $this->method;
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
        return $this->typeInfo ??= new TypeInfo($this->parameter->getType(), $this);
    }
}
