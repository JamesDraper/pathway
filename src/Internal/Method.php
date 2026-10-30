<?php

declare(strict_types=1);

namespace Pathway\Internal;

use Pathway\Invocation\ArgumentMapperException;
use Pathway\Invocation\ArgumentMapper;
use Pathway\DispatcherException;
use Pathway\Info\ParameterInfo;
use Pathway\Info\MethodInfo;

use Throwable;

use function array_filter;
use function array_slice;
use function count;

/**
 * @internal
 */
final class Method
{
    private readonly MethodInfo $methodInfo;

    private readonly ArgumentMapper $argumentMapper;

    /**
     * @throws DispatcherException
     */
    public function __construct(MethodInfo $methodInfo, ArgumentMapper $argumentMapper)
    {
        $this->methodInfo = $methodInfo;
        $this->argumentMapper = $argumentMapper;

        if ($this->methodInfo->isStatic()) {
            throw DispatcherException::methodIsStatic(
                $this->methodInfo->getClassInfo()->getName(),
                $this->methodInfo->getName(),
            );
        }
    }

    /**
     * @throws ArgumentMapperException
     * @throws DispatcherException
     */
    public function invoke(object $object, mixed $value): mixed
    {
        $arguments = $this->argumentMapper->resolve($this->methodInfo, $value);

        $method = $this->methodInfo->getName();

        try {
            return $object->{$method}(...$arguments);
        } catch (Throwable $throwable) {
            $mismatch = $this->findMismatch($arguments, $throwable);

            if ($mismatch !== null) {
                throw $mismatch;
            }

            throw $throwable;
        }
    }

    /**
     * @param list<mixed> $arguments
     */
    private function findMismatch(array $arguments, Throwable $throwable): ?DispatcherException
    {
        $class = $this->methodInfo->getClassInfo()->getName();
        $method = $this->methodInfo->getName();
        $given = count($arguments);

        foreach ($this->methodInfo->getParameterInfos() as $position => $parameterInfo) {
            if ($parameterInfo->isVariadic()) {
                foreach (array_slice($arguments, $position) as $offset => $argument) {
                    if (!$parameterInfo->getTypeInfo()->isCompatible($argument)) {
                        return DispatcherException::typeMismatch(
                            $class,
                            $method,
                            $position + $offset + 1,
                            $parameterInfo->getName(),
                            $parameterInfo->getTypeInfo()->toString(),
                            $argument,
                            $throwable,
                        );
                    }
                }

                return null;
            }

            if ($position >= $given) {
                if ($parameterInfo->hasDefault()) {
                    continue;
                }

                return DispatcherException::tooFewArguments(
                    $class,
                    $method,
                    $this->countRequiredParameters(),
                    $given,
                    $throwable,
                );
            }

            if (!$parameterInfo->getTypeInfo()->isCompatible($arguments[$position])) {
                return DispatcherException::typeMismatch(
                    $class,
                    $method,
                    $position + 1,
                    $parameterInfo->getName(),
                    $parameterInfo->getTypeInfo()->toString(),
                    $arguments[$position],
                    $throwable,
                );
            }
        }

        return null;
    }

    private function countRequiredParameters(): int
    {
        return count(array_filter(
            $this->methodInfo->getParameterInfos(),
            static fn(ParameterInfo $parameterInfo): bool => !$parameterInfo->hasDefault() && !$parameterInfo->isVariadic(),
        ));
    }
}
