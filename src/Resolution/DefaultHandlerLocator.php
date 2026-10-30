<?php

declare(strict_types=1);

namespace Pathway\Resolution;

use Pathway\Internal\TypeFormatter;
use Pathway\Info\ClassInfoFactory;
use Pathway\Info\MethodInfo;

use Throwable;

use function sprintf;

/**
 * @internal
 */
class DefaultHandlerLocator implements HandlerLocator
{
    private const string FACTORY_METHOD = 'make';

    public function locate(string $id): object
    {
        $classInfo = ClassInfoFactory::make($id);

        if ($classInfo === null) {
            throw $this->classDoesNotExist($id);
        }

        $class = $classInfo->getName();

        $methodInfo = $classInfo->getMethodInfo(self::FACTORY_METHOD);

        if ($methodInfo === null) {
            throw $this->factoryMethodDoesNotExist($id);
        }

        if (!$methodInfo->isPublic() || !$methodInfo->isStatic()) {
            throw $this->factoryMethodNotPublicStatic($id);
        }

        if ($this->hasRequiredParameters($methodInfo)) {
            throw $this->factoryMethodHasRequiredParameters($id);
        }

        $method = $methodInfo->getName();

        try {
            $handler = $class::$method();
        } catch (Throwable $throwable) {
            throw $this->factoryMethodThrew($id, $throwable);
        }

        if (!$handler instanceof $class) {
            throw $this->factoryMethodReturnedInvalidType($id, $handler);
        }

        return $handler;
    }

    private function hasRequiredParameters(MethodInfo $methodInfo): bool
    {
        foreach ($methodInfo->getParameterInfos() as $parameterInfo) {
            if (!$parameterInfo->hasDefault() && !$parameterInfo->isVariadic()) {
                return true;
            }
        }

        return false;
    }

    private function classDoesNotExist(string $id): DefaultHandlerLocatorException
    {
        return new DefaultHandlerLocatorException(sprintf('Handler class "%s" does not exist.', $id));
    }

    private function factoryMethodDoesNotExist(string $id): DefaultHandlerLocatorException
    {
        return new DefaultHandlerLocatorException(sprintf(
            'Handler class "%s" does not have a "%s" method.',
            $id,
            self::FACTORY_METHOD,
        ));
    }

    private function factoryMethodNotPublicStatic(string $id): DefaultHandlerLocatorException
    {
        return new DefaultHandlerLocatorException(sprintf(
            'Method "%s::%s" must be public and static.',
            $id,
            self::FACTORY_METHOD,
        ));
    }

    private function factoryMethodHasRequiredParameters(string $id): DefaultHandlerLocatorException
    {
        return new DefaultHandlerLocatorException(sprintf(
            'Method "%s::%s" must not have required parameters.',
            $id,
            self::FACTORY_METHOD,
        ));
    }

    private function factoryMethodThrew(string $id, Throwable $throwable): DefaultHandlerLocatorException
    {
        return new DefaultHandlerLocatorException(
            message: sprintf('Method "%s::%s" threw an exception: %s', $id, self::FACTORY_METHOD, $throwable->getMessage()),
            previous: $throwable,
        );
    }

    private function factoryMethodReturnedInvalidType(string $id, mixed $handler): DefaultHandlerLocatorException
    {
        return new DefaultHandlerLocatorException(sprintf(
            'Method "%s::%s" must return an instance of "%s", "%s" returned.',
            $id,
            self::FACTORY_METHOD,
            $id,
            TypeFormatter::format($handler),
        ));
    }
}
