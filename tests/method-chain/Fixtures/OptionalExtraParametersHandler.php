<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

use Pathway\DispatcherInterface;

final class OptionalExtraParametersHandler
{
    public function process(
        Command $command,
        ?DispatcherInterface $dispatcher = null,
        string $suffix = '!',
        mixed ...$rest,
    ): string {
        return $command->value . $suffix;
    }
}
