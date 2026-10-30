<?php

declare(strict_types=1);

namespace Pathway\Internal;

use Pathway\Invocation\ArgumentMapperException;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Info\ClassInfoFactory;
use Pathway\DispatcherException;
use Pathway\Info\MethodInfo;
use Pathway\Info\ClassInfo;

use function array_slice;
use function sprintf;

/**
 * The chain of methods on a handler that a command is run through:
 *
 * 1. prepare() - optional.
 * 2. process() - required.
 * 3. finalise() - optional.
 *
 * The first method in the chain is the start: it is called with the command and the handler. Each later method receives
 * the result of the method before it, passed through the argument mapper. The result of the last method in the chain is
 * returned. So without prepare(), process() is the start; without finalise(), process() is the end.
 *
 * Methods in the chain must be public and not static.
 *
 * @internal
 */
final class MethodChain
{
    private readonly object $handler;

    /**
     * @var non-empty-list<Method>
     */
    private readonly array $methods;

    /**
     * @throws DispatcherException
     */
    public function __construct(object $handler, ArgumentMapper $argumentMapper)
    {
        $this->handler = $handler;

        /** @var ClassInfo $classInfo The handler is an instance of the class, so the class exists. */
        $classInfo = ClassInfoFactory::make($handler::class);

        $prepare = $this->getMethodInfo($classInfo, 'prepare');
        $process = $this->getMethodInfo($classInfo, 'process');
        $finalise = $this->getMethodInfo($classInfo, 'finalise');

        if ($process === null) {
            throw $this->processDoesNotExist($classInfo);
        }

        $startArgumentMapper = $this->makeStartArgumentMapper();

        $methods = $prepare === null
            ? [new Method($process, $startArgumentMapper)]
            : [new Method($prepare, $startArgumentMapper), new Method($process, $argumentMapper)];

        if ($finalise !== null) {
            $methods[] = new Method($finalise, $argumentMapper);
        }

        $this->methods = $methods;
    }

    /**
     * @throws ArgumentMapperException
     * @throws DispatcherException
     */
    public function invoke(object $command): mixed
    {
        $result = $this->methods[0]->invoke($this->handler, [$command, $this->handler]);

        foreach (array_slice($this->methods, 1) as $method) {
            $result = $method->invoke($this->handler, $result);
        }

        return $result;
    }

    private function makeStartArgumentMapper(): ArgumentMapper
    {
        return new class implements ArgumentMapper {
            public function resolve(MethodInfo $methodInfo, mixed $value): array
            {
                /** @var list<mixed> $value */
                return $value;
            }
        };
    }

    private function getMethodInfo(ClassInfo $classInfo, string $method): ?MethodInfo
    {
        $methodInfo = $classInfo->getMethodInfo($method);

        if ($methodInfo !== null && !$methodInfo->isPublic()) {
            throw $this->methodNotPublic($classInfo, $method);
        }

        return $methodInfo;
    }

    private function processDoesNotExist(ClassInfo $classInfo): DispatcherException
    {
        return new DispatcherException(sprintf(
            'Handler class "%s" does not have a "process" method.',
            $classInfo->getName(),
        ));
    }

    private function methodNotPublic(ClassInfo $classInfo, string $method): DispatcherException
    {
        return new DispatcherException(sprintf(
            'Method "%s::%s" must be public.',
            $classInfo->getName(),
            $method,
        ));
    }
}
