<?php

declare(strict_types=1);

namespace Pathway;

use Pathway\Resolution\HandlerIdentifierException;
use Pathway\Invocation\ArgumentMapperException;
use Pathway\Resolution\HandlerLocatorException;
use Pathway\Resolution\HandlerIdentifier;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Resolution\HandlerLocator;
use Pathway\Internal\MethodChain;

final class Dispatcher implements DispatcherInterface
{
    private readonly HandlerIdentifier $handlerIdentifier;

    private readonly HandlerLocator $handlerLocator;

    private readonly ArgumentMapper $argumentMapper;

    /**
     * @var array<class-string, string>
     */
    private array $handlerIds = [];

    /**
     * @var array<string, MethodChain>
     */
    private array $methodChains = [];

    public function __construct(
        HandlerIdentifier $handlerIdentifier,
        HandlerLocator $handlerLocator,
        ArgumentMapper $argumentMapper,
    ) {
        $this->handlerIdentifier = $handlerIdentifier;
        $this->handlerLocator = $handlerLocator;
        $this->argumentMapper = $argumentMapper;
    }

    /**
     * @throws HandlerIdentifierException
     * @throws HandlerLocatorException
     * @throws DispatcherException
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

        return $this->methodChains[$id] ??= new MethodChain(
            $this->handlerLocator->locate($id),
            $this,
            $this->argumentMapper,
        );
    }
}
