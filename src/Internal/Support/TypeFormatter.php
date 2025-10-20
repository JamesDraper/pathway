<?php
declare(strict_types=1);

namespace Pathway\Internal\Support;

use Closure;

use function enum_exists;
use function get_class;
use function gettype;
use function sprintf;

/**
 * @internal
 */
class TypeFormatter
{
    public static function format(mixed $value): string
    {
        $type = gettype($value);

        return match ($type) {
            'NULL' => 'null',
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'array' => 'array',
            'object' => self::formatObject((object) $value),
            'resource', 'resource (closed)' => 'resource',
            default => $type,
        };
    }

    private static function formatObject(object $value): string
    {
        if ($value instanceof Closure) {
            return 'closure';
        }

        $class = get_class($value);

        if (enum_exists($class)) {
            return sprintf('enum(%s)', $class);
        }

        return sprintf('object(%s)', $class);
    }
}
