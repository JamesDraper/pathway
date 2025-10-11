<?php
declare(strict_types=1);

namespace Pathway\Internal\Reflection;

use ReflectionClass;

/**
 * @internal
 */
class HandlerClassFactory
{
    /**
     * @template THandler of object
     * @param THandler $handler
     * @return HandlerClass<THandler>
     */
    public function create(object $handler): HandlerClass
    {
        $reflectionClass = new ReflectionClass($handler);

        $prepare = new HandlerMethod($reflectionClass, $handler, 'prepare');
        $process = new HandlerMethod($reflectionClass, $handler, 'process');
        $finalize = new HandlerMethod($reflectionClass, $handler, 'finalize');

        return new HandlerClass(
            prepare: $prepare,
            process: $process,
            finalize: $finalize,
        );
    }
}
