<?php

declare(strict_types=1);

namespace Pathway\Resolution;

interface HandlerIdentifier
{
    /**
     * @param class-string $class
     * @return string
     * @throws HandlerIdentifierException
     */
    public function identify(string $class): string;
}
