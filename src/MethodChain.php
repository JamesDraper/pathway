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
