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

    /**
     * @internal
     */
    public static function commandParameterMissing(string $class, string $method): self
    {
        return new self(sprintf('Method "%s::%s" must accept the command as its first parameter.', $class, $method));
    }

    /**
     * @internal
     */
    public static function commandParameterMismatch(
        string $class,
        string $method,
        string $parameter,
        string $type,
        string $commandClass,
    ): self {
        return new self(sprintf(
            'Parameter #1 ($%s) of method "%s::%s" must accept the command "%s", but is of type %s.',
            $parameter,
            $class,
            $method,
            $commandClass,
            $type,
        ));
    }

    /**
     * @internal
     */
    public static function dispatcherParameterMismatch(
        string $class,
        string $method,
        string $parameter,
        string $type,
        string $dispatcherClass,
    ): self {
        return new self(sprintf(
            'Parameter #2 ($%s) of method "%s::%s" must accept the dispatcher "%s", but is of type %s.',
            $parameter,
            $class,
            $method,
            $dispatcherClass,
            $type,
        ));
    }

    /**
     * @internal
     */
    public static function tooManyRequiredParameters(string $class, string $method): self
    {
        return new self(sprintf(
            'Method "%s::%s" must not have more than two required parameters: the command and the dispatcher.',
            $class,
            $method,
        ));
    }
}
