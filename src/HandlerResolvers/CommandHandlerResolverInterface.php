<?php
declare(strict_types=1);

namespace Pathway\HandlerResolvers;

interface CommandHandlerResolverInterface
{
    /**
     * @param class-string $classPath
     */
    public function resolve(string $classPath): object;
}
