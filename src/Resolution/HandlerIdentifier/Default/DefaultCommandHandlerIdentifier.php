<?php
declare(strict_types=1);

namespace Pathway\Resolution\HandlerIdentifier\Default;

use Pathway\Resolution\HandlerIdentifier\CommandHandlerIdentifier;

final class DefaultCommandHandlerIdentifier implements CommandHandlerIdentifier
{
    public function identify(string $class): string
    {
        return $class . 'Handler';
    }
}
