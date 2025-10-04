<?php
declare(strict_types=1);

namespace Pathway\Reflection;

use ReflectionException;
use ReflectionParameter;
use ReflectionMethod;
use ReflectionClass;

use LogicException;

use function array_key_exists;
use function array_is_list;
use function array_map;

/**
 * @internal
 */
class HandlerMethod
{
    private const int PARAM_NAME = 0;
    private const int PARAM_IS_VARIADIC = 1;
    private const int PARAM_HAS_DEFAULT = 2;
    private const int PARAM_DEFAULT = 3;

    private readonly object $handler;

    private readonly string $name;

    private readonly array $parameters;

    public function __construct(object $handler, string $methodName)
    {
        $class = new ReflectionClass($handler);

        $method = $this->getMethod($class, $methodName);
        $this->assertMethodIsPublicAndNonStatic($method);

        $this->handler = $handler;
        $this->name = $method->getName();
        $this->parameters = $this->buildParameters($method->getParameters());
    }

    public function __invoke(array $arguments): mixed
    {
        $arguments = array_is_list($arguments)
            ? $this->resolvePositionalArguments($arguments)
            : $this->resolveNamedArguments($arguments);

        return $this->handler->{$this->name}(...$arguments);
    }

    private function resolvePositionalArguments(array $arguments): array
    {
        $resolved = [];
        $index = 0;

        foreach ($this->parameters as $parameter) {
            if ($parameter[self::PARAM_IS_VARIADIC]) {
                while (array_key_exists($index, $arguments)) {
                    $resolved[] = $arguments[$index++];
                }

                break;
            }

            if (array_key_exists($index, $arguments)) {
                $resolved[] = $arguments[$index++];
                continue;
            }

            if ($parameter[self::PARAM_HAS_DEFAULT]) {
                $resolved[] = $parameter[self::PARAM_DEFAULT];
                continue;
            }

            throw new LogicException('missing-argument');
        }

        if (array_key_exists($index, $arguments)) {
            throw new LogicException('too-many-args');
        }

        return $resolved;
    }

    private function resolveNamedArguments(array $arguments): array
    {
        $resolved = [];

        foreach ($this->parameters as $parameter) {
            $name = $parameter[self::PARAM_NAME];

            if (array_key_exists($name, $arguments)) {
                $resolved[] = $arguments[$name];
                continue;
            }

            if ($parameter[self::PARAM_HAS_DEFAULT]) {
                $resolved[] = $parameter[self::PARAM_DEFAULT];
                continue;
            }

            throw new LogicException('missing argument');
        }

        return $resolved;
    }

    private function getMethod(ReflectionClass $class, string $method): ReflectionMethod
    {
        try {
            return $class->getMethod($method);
        } catch (ReflectionException) {
            throw new LogicException('method does not exist.');
        }
    }

    private function assertMethodIsPublicAndNonStatic(ReflectionMethod $method): void
    {
        if (!$method->isPublic() || $method->isStatic()) {
            throw new LogicException(sprintf(
                'Handler method %s::%s() must be public and non-static.',
                $method->getDeclaringClass()->getName(),
                $method->getName(),
            ));
        }
    }

    /**
     * @param ReflectionParameter[] $parameters
     */
    private function buildParameters(array $parameters): array
    {
        return array_map(
            fn (ReflectionParameter $parameter): array => $this->buildParameter($parameter),
            $parameters,
        );
    }

    private function buildParameter(ReflectionParameter $parameter): array
    {
        $hasDefault = $parameter->isDefaultValueAvailable();

        $parameterData = [
            self::PARAM_NAME => $parameter->getName(),
            self::PARAM_IS_VARIADIC => $parameter->isVariadic(),
            self::PARAM_HAS_DEFAULT => $hasDefault,
        ];

        if ($hasDefault) {
            $parameterData[self::PARAM_DEFAULT] = $parameter->getDefaultValue();
        }

        return $parameterData;
    }
}
