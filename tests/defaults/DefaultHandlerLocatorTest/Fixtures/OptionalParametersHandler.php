<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultHandlerLocatorTest\Fixtures;

final class OptionalParametersHandler
{
    /**
     * @param list<string> $rest
     */
    private function __construct(
        public readonly string $optional,
        public readonly array $rest,
    ) {}

    public static function make(string $optional = 'default', string ...$rest): self
    {
        return new self($optional, $rest);
    }
}
