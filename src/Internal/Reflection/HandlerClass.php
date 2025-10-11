<?php
declare(strict_types=1);

namespace Pathway\Internal\Reflection;

/**
 * @internal
 * @template THandler of object
 * @phpstan-type ArgumentList array<string, mixed>|list<mixed>
 */
class HandlerClass
{
    /**
     * @param HandlerMethod<THandler> $prepare
     * @param HandlerMethod<THandler> $process
     * @param HandlerMethod<THandler> $finalize
     */
    public function __construct(
        public readonly HandlerMethod $prepare,
        public readonly HandlerMethod $process,
        public readonly HandlerMethod $finalize,
    ) {
    }
}
