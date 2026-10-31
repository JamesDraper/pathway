<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class ProcessFinalizeHandler
{
    /**
     * @var list<array{method: string, arguments: array<int|string, mixed>}>
     */
    public array $calls = [];

    public function process(mixed ...$arguments): string
    {
        $this->calls[] = ['method' => 'process', 'arguments' => $arguments];

        return 'processed';
    }

    public function finalize(mixed ...$arguments): string
    {
        $this->calls[] = ['method' => 'finalize', 'arguments' => $arguments];

        return 'finalized';
    }
}
