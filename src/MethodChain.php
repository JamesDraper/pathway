<?php

declare(strict_types=1);

namespace Pathway;

use Pathway\Invocation\ArgumentMismatchException;
use Pathway\Invocation\ArgumentMapperException;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Info\ClassInfoFactory;
use Pathway\Info\ParameterInfo;
use Pathway\Info\MethodInfo;
use Pathway\Info\ClassInfo;

use function array_slice;

/**
 * The chain of methods on a handler that a command is run through:
 *
 * 1. prepare() - optional.
 * 2. process() - required.
 * 3. finalize() - optional.
 *
 * The first method in the chain is the start: it is called with the command and the dispatcher. Each later method
 * receives the result of the method before it, passed through the argument mapper. The result of the last method in the
 * chain is returned. So without prepare(), process() is the start; without finalize(), process() is the end.
 *
 * Methods in the chain must be public and not static. The start method must accept the command as its first parameter;
 * a second parameter is optional and must accept the dispatcher. Any further parameters must be optional.
 *
 * @internal
 */
final class MethodChain
{
    private readonly object $handler;

    private readonly DispatcherInterface $dispatcher;

    /**
     * @var non-empty-list<Method>
     */
    private readonly array $methods;

    private readonly ParameterInfo $commandParameterInfo;

    /**
     * Command classes already checked against the command parameter.
     *
     * @var array<class-string, true>
     */
    private array $checkedCommandClasses = [];

    /**
     * @throws HandlerDefinitionException
     */
    public function __construct(object $handler, DispatcherInterface $dispatcher, ArgumentMapper $argumentMapper)
    {
        $this->handler = $handler;
        $this->dispatcher = $dispatcher;

        /**
         * @var ClassInfo
         */
        $classInfo = ClassInfoFactory::make($handler::class);

        $prepare = $this->getMethodInfo($classInfo, 'prepare');
        $process = $this->getMethodInfo($classInfo, 'process');
        $finalize = $this->getMethodInfo($classInfo, 'finalize');

        if ($process === null) {
            throw HandlerDefinitionException::processDoesNotExist($classInfo->getName());
        }

        $startArgumentMapper = $this->makeStartArgumentMapper();

        $methods = ($prepare !== null)
            ? [new Method($prepare, $startArgumentMapper), new Method($process, $argumentMapper)]
            : [new Method($process, $startArgumentMapper)];

        if ($finalize !== null) {
            $methods[] = new Method($finalize, $argumentMapper);
        }

        $this->methods = $methods;

        $this->commandParameterInfo = $this->checkStartMethod($prepare ?? $process);
    }

    /**
     * @throws HandlerDefinitionException
     * @throws ArgumentMapperException
     * @throws ArgumentMismatchException
     */
    public function invoke(object $command): mixed
    {
        $this->checkCommand($command);

        $result = $this->methods[0]->invoke($this->handler, [$command, $this->dispatcher]);

        foreach (array_slice($this->methods, 1) as $method) {
            $result = $method->invoke($this->handler, $result);
        }

        return $result;
    }

    /**
     * Checks the parameters of the start method that can be checked without a command, and returns the parameter that
     * receives the command.
     *
     * @throws HandlerDefinitionException
     */
    private function checkStartMethod(MethodInfo $methodInfo): ParameterInfo
    {
        $class = $methodInfo->getClassInfo()->getName();
        $method = $methodInfo->getName();
        $parameterInfos = $methodInfo->getParameterInfos();

        if ($parameterInfos === []) {
            throw HandlerDefinitionException::commandParameterMissing($class, $method);
        }

        // A variadic first parameter receives both the command and the dispatcher.
        $dispatcherParameterInfo = $parameterInfos[0]->isVariadic()
            ? $parameterInfos[0]
            : ($parameterInfos[1] ?? null);

        if (
            $dispatcherParameterInfo !== null
            && !$dispatcherParameterInfo->getTypeInfo()->isCompatible($this->dispatcher)
        ) {
            throw HandlerDefinitionException::dispatcherParameterMismatch(
                $class,
                $method,
                $dispatcherParameterInfo->getName(),
                $dispatcherParameterInfo->getTypeInfo()->toString(),
                $this->dispatcher::class,
            );
        }

        foreach (array_slice($parameterInfos, 2) as $parameterInfo) {
            if (!$parameterInfo->hasDefault() && !$parameterInfo->isVariadic()) {
                throw HandlerDefinitionException::tooManyRequiredParameters($class, $method);
            }
        }

        return $parameterInfos[0];
    }

    /**
     * Checks the command against the command parameter. One handler can handle several command classes, so each class
     * is checked the first time it is seen.
     *
     * @throws HandlerDefinitionException
     */
    private function checkCommand(object $command): void
    {
        $commandClass = $command::class;

        if (isset($this->checkedCommandClasses[$commandClass])) {
            return;
        }

        $typeInfo = $this->commandParameterInfo->getTypeInfo();

        if (!$typeInfo->isCompatible($command)) {
            $methodInfo = $this->commandParameterInfo->getMethodInfo();

            throw HandlerDefinitionException::commandParameterMismatch(
                $methodInfo->getClassInfo()->getName(),
                $methodInfo->getName(),
                $this->commandParameterInfo->getName(),
                $typeInfo->toString(),
                $commandClass,
            );
        }

        $this->checkedCommandClasses[$commandClass] = true;
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

    /**
     * @throws HandlerDefinitionException
     */
    private function getMethodInfo(ClassInfo $classInfo, string $method): ?MethodInfo
    {
        $methodInfo = $classInfo->getMethodInfo($method);

        if ($methodInfo === null) {
            return null;
        }

        if (!$methodInfo->isPublic()) {
            throw HandlerDefinitionException::methodNotPublic($classInfo->getName(), $method);
        }

        if ($methodInfo->isStatic()) {
            throw HandlerDefinitionException::methodIsStatic($classInfo->getName(), $method);
        }

        return $methodInfo;
    }
}
