<?php

declare(strict_types=1);

namespace Pathway\Info;

use ReflectionParameter;
use ReflectionMethod;

use function array_map;

/**
 * @internal
 */
class MethodInfo
{
    /**
     * @var list<ParameterInfo>|null
     */
    private ?array $parameterInfos = null;

    public function __construct(private readonly ReflectionMethod $method) {}

    public function isPublic(): bool
    {
        return $this->method->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->method->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->method->isPrivate();
    }

    public function isStatic(): bool
    {
        return $this->method->isStatic();
    }

    public function getName(): string
    {
        return $this->method->getName();
    }

    /**
     * @return list<ParameterInfo>
     */
    public function getParameterInfos(): array
    {
        return $this->parameterInfos ??= array_map(
            static fn(ReflectionParameter $parameter): ParameterInfo => new ParameterInfo($parameter),
            $this->method->getParameters(),
        );
    }
}
