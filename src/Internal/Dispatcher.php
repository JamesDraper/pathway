<?php
declare(strict_types=1);

namespace Pathway\Internal;

use Pathway\HandlerResolvers\CommandHandlerResolverInterface;
use Pathway\HandlerResolvers\EventHandlerResolverInterface;
use Pathway\Internal\HandlerRunner\HandlerRunnerFactory;
use Pathway\Internal\HandlerRunner\HandlerRunner;
use Pathway\DispatcherInterface;

use function is_object;

class Dispatcher
{
    private readonly CommandHandlerResolverInterface $commandHandlerResolver;

    private readonly EventHandlerResolverInterface $eventHandlerResolver;

    private readonly HandlerRunnerFactory $handlerRunnerFactory;

    private readonly DispatcherInterface $dispatcher;

    /**
     * @var array<class-string, HandlerRunner<object>>
     */
    private array $commandHandlerMap = [];

    /**
     * @var array<class-string, list<HandlerRunner<object>>>
     */
    private array $eventHandlerMap = [];

    public function __construct(
        CommandHandlerResolverInterface $commandHandlerResolver,
        EventHandlerResolverInterface $eventHandlerResolver,
        HandlerRunnerFactory $handlerRunnerFactory,
        DispatcherInterface $dispatcher,
    ) {
        $this->commandHandlerResolver = $commandHandlerResolver;
        $this->eventHandlerResolver = $eventHandlerResolver;
        $this->handlerRunnerFactory = $handlerRunnerFactory;
        $this->dispatcher = $dispatcher;
    }

    public function command(object $command): mixed
    {
        $classPath = $command::class;

        if (!isset($this->commandHandlerMap[$classPath])) {
            $handler = $this->commandHandlerResolver->resolve($classPath);
            $handlerRunner = $this->handlerRunnerFactory->make($handler);

            $this->commandHandlerMap[$classPath] = $handlerRunner;
        }

        return $this->commandHandlerMap[$classPath]->run($command, $this->dispatcher);
    }

    public function event(object $event): void
    {
        $classPath = $event::class;

        if (!isset($this->eventHandlerMap[$classPath])) {
            $this->eventHandlerMap[$classPath] = [];

            // @phpstan-ignore arrayValues.list
            $handlers = array_values($this->eventHandlerResolver->resolve($classPath));

            foreach ($handlers as $i => $handler) {
                if (!is_object($handler)) { // @phpstan-ignore function.alreadyNarrowedType
                    throw Exception::eventHandlerNotObject($classPath, $i, $handler);
                }

                $handlerRunner = $this->handlerRunnerFactory->make($handler);

                $this->eventHandlerMap[$classPath][] = $handlerRunner;

                $handlerRunner->run($event, $this->dispatcher);
            }
        } else {
            foreach ($this->eventHandlerMap[$classPath] as $i => $handlerRunner) {
                $handlerRunner->run($event, $this->dispatcher);
            }
        }
    }
}
