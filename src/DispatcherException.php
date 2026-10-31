<?php

declare(strict_types=1);

namespace Pathway;

use Pathway\Invocation\InvocationException;
use Pathway\Internal\TypeFormatter;

use LogicException;
use Throwable;

use function sprintf;

final class DispatcherException extends LogicException implements InvocationException
{
    /**
     * @internal
     */
    public static function tooFewArguments(
        string $class,
        string $method,
        int $required,
        int $given,
        Throwable $previous,
    ): self {
        return new self(
            message: sprintf(
                'Too few arguments for method "%s::%s": %d required, %d given.',
                $class,
                $method,
                $required,
                $given,
            ),
            previous: $previous,
        );
    }

    /**
     * @internal
     */
    public static function typeMismatch(
        string $class,
        string $method,
        int $position,
        string $parameter,
        string $type,
        mixed $argument,
        Throwable $previous,
    ): self {
        return new self(
            message: sprintf(
                'Argument #%d ($%s) for method "%s::%s" must be of type %s, %s given.',
                $position,
                $parameter,
                $class,
                $method,
                $type,
                TypeFormatter::format($argument),
            ),
            previous: $previous,
        );
    }
}
