<?php
declare(strict_types=1);

namespace Pathway\HandlerResolvers\InMemory;

use Pathway\HandlerResolvers\EventHandlerResolverInterface;
use Pathway\Support\TypeChecker;

final class EventHandlerResolver implements EventHandlerResolverInterface
{
    /**
     * @param array<class-string, list<object>> $map
     */
    public function __construct(private readonly array $map)
    {
    }

    /**
     * @param class-string $classPath
     * @return list<object>
     * @throws Exception
     */
    public function resolve(string $classPath): array
    {
        $handlers = $this->map[$classPath] ?? [];

        if (!TypeChecker::isArray($handlers)) {
            throw Exception::eventHandlerNotArray($classPath, $handlers);
        }

        if (!TypeChecker::arrayIsList($handlers)) {
            throw Exception::eventHandlerArrayNotList($classPath);
        }

        foreach ($handlers as $i => $handler) {
            if (!TypeChecker::isObject($handler)) {
                throw Exception::eventHandlerNotObject($classPath, $i, $handler);
            }
        }

        return $handlers;
    }
}
