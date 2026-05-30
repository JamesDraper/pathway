<?php
declare(strict_types=1);

namespace Pathway\Resolution\HandlerIdentifier;

interface EventHandlerIdentifier
{
    /**
     * @param class-string $class
     * @return list<string>
     * @throws EventHandlerIdentifierException
     */
    public function identify(string $class): array;
}
