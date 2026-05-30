<?php
declare(strict_types=1);

namespace Pathway;

interface Dispatcher
{
    /**
     * @throws PathwayException
     */
    public function command(object $command): mixed;

    /**
     * @throws PathwayException
     */
    public function event(object $event): void;
}
