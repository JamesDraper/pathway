<?php
declare(strict_types=1);

namespace Pathway\Internal\Handler;

use ReflectionClass;

/**
 * @internal
 */
class HandlerFactory
{
    /**
     * @template THandler of object
     * @param THandler $handler
     * @return Handler<THandler>
     */
    public function create(object $handler): Handler
    {
        $reflectionClass = new ReflectionClass($handler);

        $prepare = new Method($reflectionClass, $handler, 'prepare');
        $process = new Method($reflectionClass, $handler, 'process');
        $finalize = new Method($reflectionClass, $handler, 'finalize');

        return new Handler(
            prepare: $prepare,
            process: $process,
            finalize: $finalize,
        );
    }
}
