<?php
declare(strict_types=1);

namespace Pathway\HandlerResolvers\PsrContainer;

use Pathway\HandlerResolvers\CommandHandlerResolverInterface;
use Pathway\Support\TypeFormatter;

use Psr\Container\ContainerInterface;

use LogicException;

use function is_object;

class CommandHandlerResolver implements CommandHandlerResolverInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function resolve(string $classPath): object
    {
        $handlerId = $this->getHandlerId($classPath);

        if (!$this->container->has($handlerId)) {
            throw new LogicException(sprintf(
                'No handler found for command "%s" (expected "%s").',
                $classPath,
                $handlerId
            ));
        }

        $handler = $this->container->get($handlerId);

        if (!is_object($handler)) {
            throw new LogicException(sprintf(
                'Handler for "%s" must be an object, got %s.',
                $classPath,
                TypeFormatter::format($handler)
            ));
        }

        return $handler;
    }

    protected function getHandlerId(string $commandClassPath): string
    {
        return $commandClassPath . 'Handler';
    }
}
