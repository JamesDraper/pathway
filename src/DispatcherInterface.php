<?php

declare(strict_types=1);

namespace Pathway;

interface DispatcherInterface
{
    public function dispatch(object $command): mixed;
}
