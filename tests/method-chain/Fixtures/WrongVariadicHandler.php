<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class WrongVariadicHandler
{
    public function process(Command ...$arguments): void {}
}
