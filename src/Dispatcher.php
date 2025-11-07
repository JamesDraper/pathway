<?php
declare(strict_types=1);

namespace Pathway;

use Pathway\HandlerResolvers\CommandHandlerResolverInterface;
use Pathway\HandlerResolvers\EventHandlerResolverInterface;
use Pathway\Internal\HandlerRunner\HandlerRunnerFactory;
use Pathway\Internal\Dispatcher as InternalDispatcher;

final class Dispatcher implements DispatcherInterface
{
    private static ?self $instance = null;

    private readonly InternalDispatcher $internalDispatcher;

    public static function get(
        CommandHandlerResolverInterface $commandHandlerResolver,
        EventHandlerResolverInterface $eventHandlerResolver,
    ): self {
        if (is_null(self::$instance)) {
            self::$instance = new self(
                $commandHandlerResolver,
                $eventHandlerResolver,
            );
        }

        return new self::$instance;
    }

    public function command(object $command): mixed
    {
        return $this->internalDispatcher->command($command);
    }

    public function event(object $event): void
    {
        $this->internalDispatcher->event($event);
    }

    private function __construct(
        CommandHandlerResolverInterface $commandHandlerResolver,
        EventHandlerResolverInterface $eventHandlerResolver,
    ) {
        $this->internalDispatcher = new InternalDispatcher(
            $commandHandlerResolver,
            $eventHandlerResolver,
            new HandlerRunnerFactory,
            $this,
        );
    }
}
