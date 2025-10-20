<?php
declare(strict_types=1);

namespace Pathway\Internal\Dispatcher;

use Pathway\HandlerResolvers\CommandHandlerResolverInterface;
use Pathway\HandlerResolvers\EventHandlerResolverInterface;
use Pathway\Internal\HandlerRunner\HandlerRunnerFactory;
use Pathway\Internal\Exception;

use function array_values;
use function is_object;
use function sprintf;

/**
 * @internal
 */
class Resolver
{
    /**
     * @var array<class-string, object>
     */
    private array $commandHandlerMap = [];

    /**
     * @var array<class-string, list<object>>
     */
    private array $eventHandlerMap = [];

    public function __construct(
        private readonly CommandHandlerResolverInterface $commandHandlerResolver,
        private readonly EventHandlerResolverInterface $eventHandlerResolver,
        private readonly HandlerRunnerFactory $handlerRunnerFactory,
    ) {
    }

    /**
     * @param class-string<object> $classPath
     */
    public function command(string $classPath): object
    {
        if (!isset($this->commandHandlerMap[$classPath])) {
            $handler = $this->commandHandlerResolver->resolve($classPath);

            $handlerClass = $this->handlerRunnerFactory->make($handler);

            $this->commandHandlerMap[$classPath] = $handlerClass;
        }

        return $this->commandHandlerMap[$classPath];
    }

    /**
     * @param class-string<object> $classPath
     * @return list<object>
     */
    public function event(string $classPath): array
    {
        if (!isset($this->eventHandlerMap[$classPath])) {
            $this->eventHandlerMap[$classPath] = [];

            $handlers = array_values($this->eventHandlerResolver->resolve($classPath)); // @phpstan-ignore arrayValues.list

            foreach ($handlers as $i => $handler) {
                if (!is_object($handler)) { // @phpstan-ignore function.alreadyNarrowedType
                    throw Exception::eventHandlerNotObject($classPath, $i, $handler);
                }

                $handlerClass = $this->handlerRunnerFactory->make($handler);

                $this->eventHandlerMap[$classPath][] = $handlerClass;
            }
        }

        return $this->eventHandlerMap[$classPath];
    }
}
