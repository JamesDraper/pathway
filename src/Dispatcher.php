<?php

declare(strict_types=1);

namespace Pathway;

use Pathway\Internal\MethodChain;
use Pathway\Internal\MethodChainException;
use Pathway\Internal\MethodException;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Invocation\ArgumentMapperException;
use Pathway\Resolution\HandlerIdentifier;
use Pathway\Resolution\HandlerIdentifierException;
use Pathway\Resolution\HandlerLocator;
use Pathway\Resolution\HandlerLocatorException;

/**
 * Dispatches a command to its handler.
 *
 * The handler is identified from the command's class, then located, and run as a method chain. The handler id is
 * cached per command class, and the method chain per handler id, so each handler is located once.
 */
final class Dispatcher implements DispatcherInterface
{
    /**
     * @var array<class-string, string>
     */
    private array $handlerIds = [];

    /**
     * @var array<string, MethodChain>
     */
    private array $methodChains = [];

    public function __construct(
        private readonly HandlerIdentifier $handlerIdentifier,
        private readonly HandlerLocator $handlerLocator,
        private readonly ArgumentMapper $argumentMapper,
    ) {}

    /**
     * @throws HandlerIdentifierException
     * @throws HandlerLocatorException
     * @throws MethodChainException
     * @throws MethodException
     * @throws ArgumentMapperException
     */
    public function dispatch(object $command): mixed
    {
        return $this->getMethodChain($command)->invoke($command);
    }

    private function getMethodChain(object $command): MethodChain
    {
        $class = $command::class;

        $id = $this->handlerIds[$class] ??= $this->handlerIdentifier->identify($class);

        return $this->methodChains[$id] ??= new MethodChain($this->handlerLocator->locate($id), $this->argumentMapper);
    }
}
