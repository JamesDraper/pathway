<?php
declare(strict_types=1);

namespace Pathway\Invocation\ArgumentResolver;

interface ArgumentResolver
{
    /**
     * @return list<mixed>
     * @throws ArgumentResolverException
     */
    public function resolve(MethodInfo $methodInfo, mixed ...$arguments): array;
}
