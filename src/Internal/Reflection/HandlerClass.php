<?php
declare(strict_types=1);

namespace Pathway\Internal\Reflection;

use Pathway\DispatcherInterface;

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

    public function handle(object $message, DispatcherInterface $dispatcher): mixed
    {
        /**
         * @var array<string, mixed>|list<mixed> $prepared
         */
        $prepared = $this->prepare->invoke([$message, $dispatcher]);

        /**
         * @var array<string, mixed>|list<mixed> $processed
         */
        $processed = $this->process->invoke($prepared);

        return $this->finalize->invoke($processed);
    }
}
