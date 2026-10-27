<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class NamedArgumentsHandler
{
    /**
     * @return array<string, mixed>
     */
    public function prepare(Command $command): array
    {
        return ['suffix' => '!', 'value' => $command->value];
    }

    /**
     * @return list<string>
     */
    public function process(string $value, string $suffix): array
    {
        return [$value . $suffix];
    }

    public function finalise(string $result): string
    {
        return 'finalised ' . $result;
    }
}
