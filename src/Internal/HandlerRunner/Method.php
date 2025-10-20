<?php
declare(strict_types=1);

namespace Pathway\Internal\HandlerRunner;

use Pathway\Internal\Exception;

use ReflectionException;
use ReflectionParameter;
use ReflectionMethod;
use ReflectionClass;

use function array_key_exists;
use function array_is_list;
use function is_string;

/**
 * @internal
 * @template THandler of object
 */
class Method
{
    /**
     * @var THandler
     */
    public readonly object $handler;

    public readonly string $name;

    /**
     * @var Parameter[]
     */
    private readonly array $parameters;

    /**
     * @param array<int|string, mixed> $arguments
     */
    private static function isNumericArray(array $arguments): bool
    {
        return array_is_list($arguments);
    }

    /**
     * @param array<int|string, mixed> $arguments
     */
    private static function isAssociativeArray(array $arguments): bool
    {
        foreach ($arguments as $key => $_) {
            if (!is_string($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ReflectionClass<THandler> $reflectionClass
     * @param THandler $handler
     */
    public function __construct(ReflectionClass $reflectionClass, object $handler, string $methodName)
    {
        $reflectionMethod = $this->getMethod($reflectionClass, $handler, $methodName);
        $this->assertMethodIsPublicAndNonStatic($reflectionMethod, $handler);

        $this->handler = $handler;
        $this->name = $reflectionMethod->getName();
        $this->parameters = $this->buildParameters($reflectionMethod->getParameters());
    }

    /**
     * @param array<string, mixed>|list<mixed> $arguments
     * @return mixed
     */
    public function invoke(array $arguments): mixed
    {
        if (self::isNumericArray($arguments)) {
            /**
             * @var list<mixed> $arguments
             */
            $resolved = $this->resolvePositionalArguments($arguments);
        } elseif (self::isAssociativeArray($arguments)) {
            /**
             * @var array<string, mixed> $arguments
             */
            $resolved = $this->resolveNamedArguments($arguments);
        } else {
            throw Exception::mixedOrNonSequentialArguments($this->handler, $this->name);
        }

        return $this->handler->{$this->name}(...$resolved);
    }

    /**
     * @param list<mixed> $arguments
     * @return mixed[]
     */
    private function resolvePositionalArguments(array $arguments): array
    {
        $resolved = [];
        $index = 0;

        foreach ($this->parameters as $parameter) {
            if ($parameter->isVariadic) {
                while (array_key_exists($index, $arguments)) {
                    $resolved[] = $arguments[$index++];
                }

                break;
            }

            if (array_key_exists($index, $arguments)) {
                $resolved[] = $arguments[$index++];
                continue;
            }

            if ($parameter->hasDefault) {
                $resolved[] = $parameter->default;
                continue;
            }

            throw Exception::missingArguments($this->handler, $this->name);
        }

        if (array_key_exists($index, $arguments)) {
            throw Exception::tooManyArguments($this->handler, $this->name);
        }

        return $resolved;
    }

    /**
     * @param array<string, mixed> $arguments
     * @return mixed[]
     */
    private function resolveNamedArguments(array $arguments): array
    {
        $resolved = [];

        foreach ($this->parameters as $parameter) {
            $name = $parameter->name;

            if (array_key_exists($name, $arguments)) {
                $resolved[] = $arguments[$name];
                unset($arguments[$name]);
                continue;
            }

            if ($parameter->hasDefault) {
                $resolved[] = $parameter->default;
                continue;
            }

            throw Exception::missingArguments($this->handler, $this->name);
        }

        if (!empty($arguments)) {
            throw Exception::tooManyArguments($this->handler, $this->name);
        }

        return $resolved;
    }

    /**
     * @param ReflectionClass<THandler> $reflectionClass
     */
    private function getMethod(ReflectionClass $reflectionClass, object $handler, string $methodName): ReflectionMethod
    {
        try {
            return $reflectionClass->getMethod($methodName);
        } catch (ReflectionException) {
            throw Exception::methodDoesNotExist($handler, $methodName);
        }
    }

    private function assertMethodIsPublicAndNonStatic(ReflectionMethod $reflectionMethod, object $handler): void
    {
        if (!$reflectionMethod->isPublic() || $reflectionMethod->isStatic()) {
            throw Exception::methodNotPublicNonStatic($handler, $reflectionMethod->getName());
        }
    }

    /**
     * @param ReflectionParameter[] $reflectionParameters
     * @return Parameter[]
     */
    private function buildParameters(array $reflectionParameters): array
    {
        $parameters = [];

        foreach ($reflectionParameters as $reflectionParameter) {
            $parameter = $this->buildParameter($reflectionParameter);

            $parameters[] = $parameter;
        }

        return $parameters;
    }

    /**
     * @return Parameter
     */
    private function buildParameter(ReflectionParameter $reflectionParameter): Parameter
    {
        $hasDefault = $reflectionParameter->isDefaultValueAvailable();

        return new Parameter(
            name: $reflectionParameter->getName(),
            isVariadic: $reflectionParameter->isVariadic(),
            hasDefault: $hasDefault,
            default: $hasDefault
                ? $reflectionParameter->getDefaultValue()
                : null,
        );
    }
}
