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
        private readonly HandlerMethod $prepare,
        private readonly HandlerMethod $process,
        private readonly HandlerMethod $finalize,
    ) {
    }

    /**
     * @phpstan-param ArgumentList $arguments
     */
    public function prepare(array $arguments): mixed
    {
        return $this->prepare->invoke($arguments);
    }

    /**
     * @phpstan-param ArgumentList $arguments
     */
    public function process(array $arguments): mixed
    {
        return $this->process->invoke($arguments);
    }

    /**
     * @phpstan-param ArgumentList $arguments
     */
    public function finalize(array $arguments): mixed
    {
        return $this->finalize->invoke($arguments);
    }

    /**
     * @return HandlerMethod<THandler>
     */
    public function getPrepareMethod(): HandlerMethod
    {
        return $this->prepare;
    }

    /**
     * @return HandlerMethod<THandler>
     */
    public function getProcessMethod(): HandlerMethod
    {
        return $this->process;
    }

    /**
     * @return HandlerMethod<THandler>
     */
    public function getFinalizeMethod(): HandlerMethod
    {
        return $this->finalize;
    }
}
