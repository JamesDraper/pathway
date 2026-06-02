<?php
declare(strict_types=1);

namespace Pathway\Resolution\HandlerIdentifier\Default;

use Pathway\Internal\Defaults\DefaultEventHandlerIdentifierHelper;
use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifier;
use Pathway\Internal\TypeFormatter;

final class DefaultEventHandlerIdentifier implements EventHandlerIdentifier
{
    private readonly DefaultEventHandlerIdentifierHelper $helper;

    /**
     * @param array<class-string, list<string>> $lookup
     */
    public function __construct(private readonly array $lookup = [])
    {
        $this->helper = new DefaultEventHandlerIdentifierHelper(
            new TypeFormatter(),
            $lookup,
        );
    }

    public function identify(string $class): array
    {
        return $this->helper->identify($class);
    }
}
