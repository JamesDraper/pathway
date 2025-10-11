<?php
declare(strict_types=1);

namespace Pathway\Internal;

use Pathway\Internal\Reflection\HandlerClassFactory;
use Pathway\CommandHandlerResolverInterface;
use Pathway\EventHandlerResolverInterface;

use LogicException;

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
        private readonly HandlerClassFactory $handlerClassFactory,
    ) {
    }

    /**
     * @param class-string<object> $classPath
     */
    public function command(string $classPath): object
    {
        if (!isset($this->commandHandlerMap[$classPath])) {
            $handler = $this->commandHandlerResolver->resolve($classPath);

            $handlerClass = $this->handlerClassFactory->create($handler);

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

            $handlers = $this->eventHandlerResolver->resolve($classPath);

            foreach ($handlers as $handler) {
                if (!is_object($handler)) { // @phpstan-ignore function.alreadyNarrowedType
                    throw new LogicException(sprintf(
                        'Event handler resolver returned non-object for "%s".',
                        $classPath
                    ));
                }

                $handlerClass = $this->handlerClassFactory->create($handler);

                $this->eventHandlerMap[$classPath][] = $handlerClass;
            }
        }

        return $this->eventHandlerMap[$classPath];
    }
}
