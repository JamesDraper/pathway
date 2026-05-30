<?php
declare(strict_types=1);

namespace Pathway\Resolution\HandlerIdentifier\Default;

use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifier;
use Pathway\Internal\TypeFormatter;

use function is_string;

final class DefaultEventHandlerIdentifier implements EventHandlerIdentifier
{
    /**
     * @param array<string, list<string>> $lookup
     */
    public function __construct(private readonly array $lookup = [])
    {
    }

    public function identify(string $class): array
    {
        $handlers = $this->lookup[$class] ?? [];

        foreach ($handlers as $handler) {
            if (!is_string($handler)) {
                $typeFormatter = $this->makeTypeFormatter();
                $type = $typeFormatter->format($handler);

                throw DefaultEventHandlerIdentifierException::handlerIdNotString($type);
            }
        }

        return $handlers;
    }

    protected function makeTypeFormatter(): TypeFormatter
    {
        return new TypeFormatter();
    }
}
