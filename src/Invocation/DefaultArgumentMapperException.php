<?php

declare(strict_types=1);

namespace Pathway\Invocation;

use Pathway\TypeFormatter;

use RuntimeException;

use function array_map;
use function implode;
use function sprintf;
use function count;

final class DefaultArgumentMapperException extends RuntimeException implements ArgumentMapperException
{
    /**
     * @internal
     */
    public static function notAnArray(string $class, string $method, mixed $value): self
    {
        return new self(sprintf(
            'Arguments for method "%s::%s" must be an array, "%s" given.',
            $class,
            $method,
            TypeFormatter::format($value),
        ));
    }

    /**
     * @internal
     */
    public static function notKeyedByName(string $class, string $method): self
    {
        return new self(sprintf(
            'Arguments for method "%s::%s" must be keyed by parameter name, positional arguments are not supported.',
            $class,
            $method,
        ));
    }

    /**
     * @internal
     * @param list<array-key> $names
     */
    public static function unknownParameters(string $class, string $method, array $names): self
    {
        $quoted = implode(', ', array_map(static fn($name): string => sprintf('"%s"', $name), $names));

        return new self(sprintf(
            'Method "%s::%s" does not have %s named %s.',
            $class,
            $method,
            count($names) === 1 ? 'a parameter' : 'parameters',
            $quoted,
        ));
    }

    /**
     * @internal
     */
    public static function missingArgument(string $class, string $method, string $name): self
    {
        return new self(sprintf(
            'Missing required argument "%s" for method "%s::%s".',
            $name,
            $class,
            $method,
        ));
    }

    /**
     * @internal
     */
    public static function variadicNotAList(string $class, string $method, string $name, mixed $value): self
    {
        return new self(sprintf(
            'Argument "%s" for variadic parameter of method "%s::%s" must be a list, "%s" given.',
            $name,
            $class,
            $method,
            TypeFormatter::format($value),
        ));
    }
}
