<?php
declare(strict_types=1);

namespace Pathway\Resolvers;

interface EventHandlerResolver
{
    /**
     * @param class-string $classPath
     * @return list<object>
     */
    public function resolve(string $classPath): iterable;
}
