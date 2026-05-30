<?php
declare(strict_types=1);

namespace Pathway\Resolution\HandlerIdentifier\Default;

use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifierException;

use Exception;

use function sprintf;

final class DefaultEventHandlerIdentifierException extends Exception implements EventHandlerIdentifierException
{
    /**
     * @internal
     */
    public static function handlerIdNotString(string $type): self
    {
        return new self(sprintf(
            'Events must be resolved to string identifiers, got %s.',
            $type,
        ));
    }

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
