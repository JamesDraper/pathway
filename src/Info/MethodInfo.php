<?php

declare(strict_types=1);

namespace Pathway\Info;

use ReflectionParameter;
use ReflectionMethod;

use function array_map;

/**
 * @internal
 */
final class MethodInfo
{
    private readonly ReflectionMethod $method;

    private readonly ClassInfo $class;

    /**
     * @var list<ParameterInfo>|null
     */
    private ?array $parameterInfos = null;

    public function __construct(ReflectionMethod $method, ClassInfo $class)
    {
        $this->method = $method;
        $this->class = $class;
    }

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

    public function getClassInfo(): ClassInfo
    {
        return $this->class;
    }

    /**
     * @return list<ParameterInfo>
     */
    public function getParameterInfos(): array
    {
        return $this->parameterInfos ??= array_map(
            fn(ReflectionParameter $parameter): ParameterInfo => new ParameterInfo($parameter, $this),
            $this->method->getParameters(),
        );
    }
}
