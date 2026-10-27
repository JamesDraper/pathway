<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class ProcessOnlyHandler
{
    /**
     * @var list<array{method: string, arguments: list<mixed>}>
     */
    public array $calls = [];

    public function process(mixed ...$arguments): string
    {
        $this->calls[] = ['method' => 'process', 'arguments' => $arguments];

        return 'processed';
    }
}
