<?php
declare(strict_types=1);

namespace Pathway\Internal\Support;

use function array_is_list;
use function array_reduce;
use function is_object;
use function is_string;
use function is_array;

/**
 * @internal
 */
class TypeChecker
{
    /**
     * @param array<string|int, mixed> $value
     */
    public static function arrayIsObjectList(array $value): bool
    {
        /**
         * @var array<string|int, mixed> $value
         */
        $isObjectArray = array_reduce($value, function (bool $isObjectArray, mixed $item): bool {
            return $isObjectArray && self::isObject($item);
        }, true);

        return $isObjectArray && self::arrayIsList($value);
    }

    /**
     * @param array<string|int, mixed> $value
     */
    public static function arrayIsAssociative(array $value): bool
    {
        foreach ($value as $key => $_) {
            if (!is_string($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string|int, mixed> $value
     */
    public static function arrayIsList(array $value): bool
    {
        return @array_is_list($value);
    }

    public static function isArray(mixed $value): bool
    {
        return is_array($value);
    }

    public static function isObject(mixed $value): bool
    {
        return is_object($value);
    }

    private function __construct()
    {
    }
}
