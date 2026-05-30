<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

use ReflectionParameter;
use ReflectionClass;

use function array_key_exists;
use function array_map;

/**
 * @internal
 */
final class ClassInfo
{
    /**
     * @param array<string, MethodInfo> $methodInfos
     */
    private array $methodInfos = [];

    // @phpstan-ignore missingType.generics
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function getName(): string
    {
        return $this->class->getName();
    }

    public function getMethodInfo(string $method): ?MethodInfo
    {
        if (!array_key_exists($method, $this->methodInfos)) {
            $this->methodInfos[$method] = $this->makeMethodInfo($method);
        }

        return $this->methodInfos[$method];
    }

    private function makeMethodInfo(string $methodName): ?MethodInfo
    {
        if (!$this->class->hasMethod($methodName)) {
            return null;
        }

        $method = $this->class->getMethod($methodName);

        $parameterInfos = array_map(function (ReflectionParameter $parameter): ParameterInfo {
            $typeInfo = new TypeInfo($parameter->getType());

            return new ParameterInfo($parameter, $typeInfo);
        }, $method->getParameters());

        return new MethodInfo($method, $parameterInfos);
    }
}
