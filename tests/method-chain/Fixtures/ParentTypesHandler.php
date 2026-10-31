<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

use Pathway\DispatcherInterface;

final class ParentTypesHandler
{
    public function process(object $command, DispatcherInterface $dispatcher): string
    {
        return 'processed';
    }
}
