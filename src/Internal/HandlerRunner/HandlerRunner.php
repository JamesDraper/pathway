<?php
declare(strict_types=1);

namespace Pathway\Internal\HandlerRunner;

use Pathway\DispatcherInterface;

/**
 * @internal
 * @template THandler of object
 * @phpstan-type ArgumentList array<string, mixed>|list<mixed>
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
         * @var ArgumentList $prepared
         */
        $prepared = $this->prepare->invoke([$message, $dispatcher]);

        /**
         * @var ArgumentList $processed
         */
        $processed = $this->process->invoke($prepared);

        return $this->finalize->invoke($processed);
    }
}
