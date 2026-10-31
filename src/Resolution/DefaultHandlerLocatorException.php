<?php

declare(strict_types=1);

namespace Pathway\Resolution;

use Pathway\TypeFormatter;

use RuntimeException;
use Throwable;

use function sprintf;

final class DefaultHandlerLocatorException extends RuntimeException implements HandlerLocatorException
{
    /**
     * @internal
     */
    public static function classDoesNotExist(string $class): self
    {
        return new self(sprintf('Handler class "%s" does not exist.', $class));
    }

    /**
     * @internal
     */
    public static function factoryMethodDoesNotExist(string $class, string $method): self
    {
        return new self(sprintf('Handler class "%s" does not have a "%s" method.', $class, $method));
    }

    /**
     * @internal
     */
    public static function factoryMethodNotPublicStatic(string $class, string $method): self
    {
        return new self(sprintf('Method "%s::%s" must be public and static.', $class, $method));
    }

    /**
     * @internal
     */
    public static function factoryMethodHasRequiredParameters(string $class, string $method): self
    {
        return new self(sprintf('Method "%s::%s" must not have required parameters.', $class, $method));
    }

    /**
     * @internal
     */
    public static function factoryMethodThrew(string $class, string $method, Throwable $previous): self
    {
        return new self(
            message: sprintf('Method "%s::%s" threw an exception: %s', $class, $method, $previous->getMessage()),
            previous: $previous,
        );
    }

    /**
     * @internal
     */
    public static function factoryMethodReturnedInvalidType(string $class, string $method, mixed $handler): self
    {
        return new self(sprintf(
            'Method "%s::%s" must return an instance of "%s", "%s" returned.',
            $class,
            $method,
            $class,
            TypeFormatter::format($handler),
        ));
    }
}
