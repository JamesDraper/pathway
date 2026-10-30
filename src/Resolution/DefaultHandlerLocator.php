<?php

declare(strict_types=1);

namespace Pathway\Resolution;

use Pathway\Info\ClassInfoFactory;
use Pathway\Info\MethodInfo;

use Throwable;

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
            throw DefaultHandlerLocatorException::classDoesNotExist($id);
        }

        $class = $classInfo->getName();

        $methodInfo = $classInfo->getMethodInfo(self::FACTORY_METHOD);

        if ($methodInfo === null) {
            throw DefaultHandlerLocatorException::factoryMethodDoesNotExist($id, self::FACTORY_METHOD);
        }

        if (!$methodInfo->isPublic() || !$methodInfo->isStatic()) {
            throw DefaultHandlerLocatorException::factoryMethodNotPublicStatic($id, self::FACTORY_METHOD);
        }

        if ($this->hasRequiredParameters($methodInfo)) {
            throw DefaultHandlerLocatorException::factoryMethodHasRequiredParameters($id, self::FACTORY_METHOD);
        }

        $method = $methodInfo->getName();

        try {
            $handler = $class::$method();
        } catch (Throwable $throwable) {
            throw DefaultHandlerLocatorException::factoryMethodThrew($id, $method, $throwable);
        }

        if (!$handler instanceof $class) {
            throw DefaultHandlerLocatorException::factoryMethodReturnedInvalidType($id, $method, $handler);
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
}
