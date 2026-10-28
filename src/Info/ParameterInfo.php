<?php

declare(strict_types=1);

namespace Pathway\Info;

use ReflectionParameter;

/**
 * @internal
 */
final class ParameterInfo
{
    private readonly ReflectionParameter $parameter;

    private readonly MethodInfo $method;

    private ?TypeInfo $typeInfo = null;

    public function __construct(ReflectionParameter $parameter, MethodInfo $method)
    {
        $this->parameter = $parameter;
        $this->method = $method;
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

    public function getMethodInfo(): MethodInfo
    {
        return $this->method;
    }

    public function getTypeInfo(): TypeInfo
    {
        return $this->typeInfo ??= new TypeInfo($this->parameter->getType(), $this);
    }
}
