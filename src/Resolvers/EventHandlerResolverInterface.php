<?php
declare(strict_types=1);

namespace Pathway\Resolvers;

interface EventHandlerResolverInterface
{
    /**
     * @param class-string $classPath
     * @return object[]
     */
    public function resolve(string $classPath): iterable;
}
