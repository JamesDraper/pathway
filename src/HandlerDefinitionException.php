<?php

declare(strict_types=1);

namespace Pathway;

use LogicException;

use function sprintf;

final class HandlerDefinitionException extends LogicException implements PathwayException
{
    /**
     * @internal
     */
    public static function methodIsStatic(string $class, string $method): self
    {
        return new self(sprintf('Method "%s::%s" must not be static.', $class, $method));
    }

    /**
     * @internal
     */
    public static function methodNotPublic(string $class, string $method): self
    {
        return new self(sprintf('Method "%s::%s" must be public.', $class, $method));
    }

    /**
     * @internal
     */
    public static function processDoesNotExist(string $class): self
    {
        return new self(sprintf('Handler class "%s" does not have a "process" method.', $class));
    }
}
