<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class WrongCommandHandler
{
    public function process(OtherCommand $command): void {}
}
