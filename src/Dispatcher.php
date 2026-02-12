<?php
declare(strict_types=1);

namespace Pathway;

use Pathway\Resolvers\CommandHandlerResolverInterface;
use Pathway\Resolvers\EventHandlerResolverInterface;

final class Dispatcher implements DispatcherInterface
{
    public function __construct(
        public readonly CommandHandlerResolverInterface $commandHandlerResolver,
        public readonly EventHandlerResolverInterface $eventHandlerResolver,
    ) {
    }

    public function command(object $command): mixed
    {
        $handler = $this->commandHandlerResolver->resolve($command::class);

        return $handler->finalize(
            ...$handler->process(
                ...$handler->prepare($command, $this),
            ),
        );
    }

    public function event(object $event): void
    {
        $handlers = $this->eventHandlerResolver->resolve($event::class);

        foreach ($handlers as $handler) {
            $handler->finalize(
                ...$handler->process(
                    ...$handler->prepare($event, $this),
                ),
            );
        }
    }
}
