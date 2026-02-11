<?php
declare(strict_types=1);

namespace Pathway;

interface DispatcherInterface
{
    public function command(object $command): mixed;

    public function event(object $event): void;
}
