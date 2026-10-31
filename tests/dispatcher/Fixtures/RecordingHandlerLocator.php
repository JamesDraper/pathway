<?php

declare(strict_types=1);

namespace Tests\Dispatcher\Fixtures;

use Pathway\Resolution\HandlerLocator;

final class RecordingHandlerLocator implements HandlerLocator
{
    /**
     * @var list<string>
     */
    public array $calls = [];

    private readonly array $handlers;

    /**
     * @param array<string, object> $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function locate(string $id): object
    {
        $this->calls[] = $id;

        return $this->handlers[$id];
    }
}
