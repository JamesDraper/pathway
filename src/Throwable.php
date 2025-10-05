<?php
declare(strict_types=1);

namespace Pathway;

interface Throwable extends \Throwable
{
    /**
     * Returns key-value pairs of class data.
     * Used for internal testing to compare that 2 throwables are equal
     * without needing to compare line, file, or stack trace.
     *
     * @internal
     * @return array<string, scalar|null>
     */
    public function snapshot(): array;
}
