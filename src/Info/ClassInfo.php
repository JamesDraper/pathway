<?php

declare(strict_types=1);

namespace Pathway\Info;

use ReflectionMethod;

use function array_key_exists;
use function method_exists;

/**
 * @internal
 */
final class ClassInfo
{
    /**
     * @var class-string
     */
    private readonly string $class;

    /**
     * @var array<string, MethodInfo|null> $methodInfos
     */
    private array $methodInfos = [];

    /**
     * @param class-string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function getName(): string
    {
        return $this->class;
    }

    public function getMethodInfo(string $method): ?MethodInfo
    {
        if (!array_key_exists($method, $this->methodInfos)) {
            $this->methodInfos[$method] = $this->makeMethodInfo($method);
        }

        return $this->methodInfos[$method];
    }

    private function makeMethodInfo(string $method): ?MethodInfo
    {
        if (!method_exists($this->class, $method)) {
            return null;
        }

        return new MethodInfo(
            new ReflectionMethod($this->class, $method),
            $this,
        );
    }
}
