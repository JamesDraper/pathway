<?php
declare(strict_types=1);

namespace Pathway\HandlerRunner;

/**
 * @internal
 */
class Parameter
{
    public function __construct(
        public readonly string $name,
        public readonly bool $isVariadic,
        public readonly bool $hasDefault,
        public readonly mixed $default,
    ) {
    }
}
