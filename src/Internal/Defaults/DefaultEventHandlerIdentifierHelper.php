<?php
declare(strict_types=1);

namespace Pathway\Internal\Defaults;

use Pathway\Resolution\HandlerIdentifier\Default\DefaultEventHandlerIdentifierException;
use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifier;
use Pathway\Internal\TypeFormatter;

use function is_string;

/**
 * @internal
 */
class DefaultEventHandlerIdentifierHelper
{
    /**
     * @param array<class-string, list<string>> $lookup
     */
    public function __construct(
        private readonly TypeFormatter $typeFormatter,
        private readonly array $lookup,
    ) {
    }

    /**
     * @throws DefaultEventHandlerIdentifierException
     */
    public function identify(string $class): array
    {
        $handlers = $this->lookup[$class] ?? [];

        foreach ($handlers as $handler) {
            if (!is_string($handler)) { // @phpstan-ignore function.alreadyNarrowedType
                $type = $this->typeFormatter->format($handler);

                throw DefaultEventHandlerIdentifierException::handlerIdNotString($type);
            }
        }

        return $handlers;
    }
}
