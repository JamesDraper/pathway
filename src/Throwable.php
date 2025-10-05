<?php
declare(strict_types=1);

namespace Pathway;

interface Throwable extends \Throwable
{
    /**
     * Returns key-value pairs of debug information about this exception.
     * Used for internal testing to compare that 2 exceptions are equal
     * without needing to compare line, file, or stack data.
     *
     * @internal
     * @return array<string, mixed>
     */
    public function debug(): array;
}
