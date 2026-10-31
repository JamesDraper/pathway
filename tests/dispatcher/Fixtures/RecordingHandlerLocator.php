<?php

declare(strict_types=1);

namespace Tests\Dispatcher\Fixtures;

use Pathway\Resolution\HandlerLocator;

/**
 * Records each call and returns the handler for the id from the given map.
 */
final class RecordingHandlerLocator implements HandlerLocator
{
    /**
     * @var list<string>
     */
    public array $calls = [];

    /**
     * @param array<string, object> $handlers
     */
    public function __construct(
        private readonly array $handlers,
    ) {}

    public function locate(string $id): object
    {
        $this->calls[] = $id;

        return $this->handlers[$id];
    }
}
