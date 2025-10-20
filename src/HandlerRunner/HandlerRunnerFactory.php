<?php
declare(strict_types=1);

namespace Pathway\HandlerRunner;

use ReflectionClass;

/**
 * @internal
 */
class HandlerRunnerFactory
{
    /**
     * @template THandler of object
     * @param THandler $handler
     * @return HandlerRunner<THandler>
     */
    public function make(object $handler): HandlerRunner
    {
        $reflectionClass = new ReflectionClass($handler);

        $prepare = new Method($reflectionClass, $handler, 'prepare');
        $process = new Method($reflectionClass, $handler, 'process');
        $finalize = new Method($reflectionClass, $handler, 'finalize');

        return new HandlerRunner(
            prepare: $prepare,
            process: $process,
            finalize: $finalize,
        );
    }
}
