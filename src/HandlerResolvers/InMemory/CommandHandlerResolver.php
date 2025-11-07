<?php
declare(strict_types=1);

namespace Pathway\HandlerResolvers\InMemory;

use Pathway\HandlerResolvers\CommandHandlerResolverInterface;
use Pathway\Internal\Support\TypeChecker;

use function array_key_exists;

final class CommandHandlerResolver implements CommandHandlerResolverInterface
{
    /**
     * @param array<class-string, object> $map
     */
    public function __construct(private readonly array $map)
    {
    }

    /**
     * @throws Exception
     */
    public function resolve(string $classPath): object
    {
        if (!array_key_exists($classPath, $this->map)) {
            throw Exception::noHandlerForCommand($classPath);
        }

        $handler = $this->map[$classPath];

        if (!TypeChecker::isObject($handler)) {
            throw Exception::commandHandlerNotObject($classPath, $handler);
        }

        return $handler;
    }
}
