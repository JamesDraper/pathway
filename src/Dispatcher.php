<?php

declare(strict_types=1);

namespace Pathway;

use Pathway\Resolution\HandlerIdentifier;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Resolution\HandlerLocator;

final class Dispatcher implements DispatcherInterface
{
    public function __construct(
        private readonly HandlerIdentifier $handlerIdentifier,
        private readonly HandlerLocator $handlerLocator,
        private readonly ArgumentMapper $argumentMapper
    ) {}

    public function dispatch(object $command): mixed
    {
        // todo: finish this.
    }
}
