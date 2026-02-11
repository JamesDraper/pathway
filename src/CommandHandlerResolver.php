<?php
declare(strict_types=1);

namespace Pathway;

interface CommandHandlerResolver
{
    /**
     * @param class-string $classPath
     */
    public function resolve(string $classPath): object;
}
