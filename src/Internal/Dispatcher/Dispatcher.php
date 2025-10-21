<?php
declare(strict_types=1);

namespace Pathway\Internal\Dispatcher;

use Pathway\Internal\HandlerRunner\HandlerRunnerFactory;
use Pathway\DispatcherInterface;

/**
 * @internal
 */
class Dispatcher
{
    public function __construct(
        public readonly DispatcherInterface $dispatcher,
        public readonly Resolver $resolver,
        public readonly HandlerRunnerFactory $handlerRunnerFactory,
    ) {
    }

    public function command(object $command): mixed
    {
        $handler = $this->resolver->command($command::class);

        return $this->runHandler($command, $handler);
    }

    public function event(object $event): void
    {
        $handlers = $this->resolver->event($event::class);

        foreach ($handlers as $handler) {
            $this->runHandler($event, $handler);
        }
    }

    private function runHandler(object $message, object $handler): mixed
    {
        $handlerRunner = $this->handlerRunnerFactory->make($handler);

        return $handlerRunner->run($message, $this->dispatcher);
    }
}
