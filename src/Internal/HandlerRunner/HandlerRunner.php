<?php
declare(strict_types=1);

namespace Pathway\Internal\HandlerRunner;

use Pathway\DispatcherInterface;

/**
 * @internal
 * @template THandler of object
 */
class HandlerRunner
{
    /**
     * @param Method<THandler> $prepare
     * @param Method<THandler> $process
     * @param Method<THandler> $finalize
     */
    public function __construct(
        public readonly Method $prepare,
        public readonly Method $process,
        public readonly Method $finalize,
    ) {
    }

    public function run(object $message, DispatcherInterface $dispatcher): mixed
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
