<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class WrongCommandPrepareHandler
{
    public function prepare(OtherCommand $command): void {}

    public function process(): void {}
}
