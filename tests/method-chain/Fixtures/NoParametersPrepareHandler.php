<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class NoParametersPrepareHandler
{
    public function prepare(): void {}

    public function process(Command $command): void {}
}
