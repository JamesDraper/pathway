<?php

declare(strict_types=1);

namespace Pathway\Resolution;

final class DefaultHandlerIdentifier implements HandlerIdentifier
{
    public function identify(string $class): string
    {
        return $class . 'Handler';
    }
}
