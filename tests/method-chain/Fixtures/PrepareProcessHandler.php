<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class PrepareProcessHandler
{
    /**
     * @var list<array{method: string, arguments: list<mixed>}>
     */
    public array $calls = [];

    public function prepare(mixed ...$arguments): string
    {
        $this->calls[] = ['method' => 'prepare', 'arguments' => $arguments];

        return 'prepared';
    }

    public function process(mixed ...$arguments): string
    {
        $this->calls[] = ['method' => 'process', 'arguments' => $arguments];

        return 'processed';
    }
}
