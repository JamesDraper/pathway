<?php
declare(strict_types=1);

namespace Pathway\Internal;

interface Throwable extends \Throwable
{
    /**
     * @internal
     * @return array<string, scalar|null>
     */
    public function snapshot(): array;
}
