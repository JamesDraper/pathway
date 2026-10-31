<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class WrongDispatcherHandler
{
    public function process(Command $command, Command $dispatcher): void {}
}
