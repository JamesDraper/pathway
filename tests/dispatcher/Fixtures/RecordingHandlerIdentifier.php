<?php

declare(strict_types=1);

namespace Tests\Dispatcher\Fixtures;

use Pathway\Resolution\HandlerIdentifier;

/**
 * Records each call and returns the class's id from the given map.
 */
final class RecordingHandlerIdentifier implements HandlerIdentifier
{
    /**
     * @var list<string>
     */
    public array $calls = [];

    /**
     * @param array<class-string, string> $ids
     */
    public function __construct(
        private readonly array $ids,
    ) {}

    public function identify(string $class): string
    {
        $this->calls[] = $class;

        return $this->ids[$class];
    }
}
