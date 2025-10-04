<?php
declare(strict_types=1);

namespace Pathway\Reflection;

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

    /**
     * @phpstan-param ArgumentList $arguments
     */
    public function prepare(array $arguments): mixed
    {
        return ($this->prepare)($arguments);
    }

    /**
     * @phpstan-param ArgumentList $arguments
     */
    public function process(array $arguments): mixed
    {
        return ($this->process)($arguments);
    }

    /**
     * @phpstan-param ArgumentList $arguments
     */
    public function finalize(array $arguments): mixed
    {
        return ($this->finalize)($arguments);
    }
}
