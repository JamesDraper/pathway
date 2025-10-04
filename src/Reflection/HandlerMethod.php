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
use function is_string;

/**
 * @internal
 * @template THandler of object
 * @phpstan-type Parameter array{
 *     name: string,
 *     is_variadic: bool,
 *     has_default: bool,
 *     default: mixed,
 * }
 */
class HandlerMethod
{
    private const string PARAM_NAME = 'name';
    private const string PARAM_IS_VARIADIC = 'is_variadic';
    private const string PARAM_HAS_DEFAULT = 'has_default';
    private const string PARAM_DEFAULT = 'default';

    /**
     * @var THandler
     */
    private readonly object $handler;

    private readonly string $name;

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
     * @param THandler $handler
     */
    public function __construct(object $handler, string $methodName)
    {
        $class = new ReflectionClass($handler);

        $method = $this->getMethod($class, $methodName);
        $this->assertMethodIsPublicAndNonStatic($method);

        $this->handler = $handler;
        $this->name = $method->getName();
        $this->parameters = $this->buildParameters($method->getParameters());
    }

    /**
     * @param array<string, mixed>|list<mixed> $arguments
     * @return mixed
     */
    public function __invoke(array $arguments): mixed
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
            throw new LogicException('invalid-arguments');
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

    /**
     * @param array<string, mixed> $arguments
     * @return mixed[]
     */
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

    /**
     * @param ReflectionClass<THandler> $class
     */
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
     * @return Parameter[]
     */
    private function buildParameters(array $parameters): array
    {
        return array_map(
            fn (ReflectionParameter $parameter): array => $this->buildParameter($parameter),
            $parameters,
        );
    }

    /**
     * @return Parameter
     */
    private function buildParameter(ReflectionParameter $parameter): array
    {
        $hasDefault = $parameter->isDefaultValueAvailable();

        return [
            self::PARAM_NAME => $parameter->getName(),
            self::PARAM_IS_VARIADIC => $parameter->isVariadic(),
            self::PARAM_HAS_DEFAULT => $hasDefault,
            self::PARAM_DEFAULT => $hasDefault
                ? $parameter->getDefaultValue()
                : null,
        ];
    }
}
