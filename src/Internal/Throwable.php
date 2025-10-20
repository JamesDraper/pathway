<?php
declare(strict_types=1);

namespace Pathway\Internal;

interface Throwable extends \Throwable
{
    /**
     * @internal
     * @return array<string, mixed>
     */
    public function snapshot(): array;
}
