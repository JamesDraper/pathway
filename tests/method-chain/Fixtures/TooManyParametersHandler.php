<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

use Pathway\DispatcherInterface;

final class TooManyParametersHandler
{
    public function process(Command $command, DispatcherInterface $dispatcher, string $extra): void {}
}
