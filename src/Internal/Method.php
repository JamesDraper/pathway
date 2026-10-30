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
use function sprintf;
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
            throw $this->methodIsStatic();
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
        $given = count($arguments);

        foreach ($this->methodInfo->getParameterInfos() as $position => $parameterInfo) {
            if ($parameterInfo->isVariadic()) {
                foreach (array_slice($arguments, $position) as $offset => $argument) {
                    if (!$parameterInfo->getTypeInfo()->isCompatible($argument)) {
                        return $this->typeMismatch($parameterInfo, $position + $offset, $argument, $throwable);
                    }
                }

                return null;
            }

            if ($position >= $given) {
                if ($parameterInfo->hasDefault()) {
                    continue;
                }

                return $this->tooFewArguments($given, $throwable);
            }

            if (!$parameterInfo->getTypeInfo()->isCompatible($arguments[$position])) {
                return $this->typeMismatch($parameterInfo, $position, $arguments[$position], $throwable);
            }
        }

        return null;
    }

    private function describe(): string
    {
        return sprintf('%s::%s', $this->methodInfo->getClassInfo()->getName(), $this->methodInfo->getName());
    }

    private function methodIsStatic(): DispatcherException
    {
        return new DispatcherException(sprintf('Method "%s" must not be static.', $this->describe()));
    }

    private function tooFewArguments(int $given, Throwable $throwable): DispatcherException
    {
        $required = count(array_filter(
            $this->methodInfo->getParameterInfos(),
            static fn(ParameterInfo $parameterInfo): bool => !$parameterInfo->hasDefault() && !$parameterInfo->isVariadic(),
        ));

        return new DispatcherException(
            message: sprintf(
                'Too few arguments for method "%s": %d required, %d given.',
                $this->describe(),
                $required,
                $given,
            ),
            previous: $throwable,
        );
    }

    private function typeMismatch(
        ParameterInfo $parameterInfo,
        int $position,
        mixed $argument,
        Throwable $throwable,
    ): DispatcherException {
        return new DispatcherException(
            message: sprintf(
                'Argument #%d ($%s) for method "%s" must be of type %s, %s given.',
                $position + 1,
                $parameterInfo->getName(),
                $this->describe(),
                $parameterInfo->getTypeInfo()->toString(),
                TypeFormatter::format($argument),
            ),
            previous: $throwable,
        );
    }
}
