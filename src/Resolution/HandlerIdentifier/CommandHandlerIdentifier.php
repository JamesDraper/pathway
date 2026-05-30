<?php
declare(strict_types=1);

namespace Pathway\Resolution\HandlerIdentifier;

interface CommandHandlerIdentifier
{
    /**
     * @param class-string $class
     * @return string
     * @throws CommandHandlerIdentifierException
     */
    public function identify(string $class): string;
}
