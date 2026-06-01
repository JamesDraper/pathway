<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use LogicException;
use ReflectionType;

use function str_starts_with;
use function is_callable;
use function is_iterable;
use function array_map;
use function get_class;
use function is_object;
use function is_string;
use function is_array;
use function is_float;
use function implode;
use function is_bool;
use function sprintf;
use function is_int;
use function substr;
use function sort;

/**
 * @internal
 */
class TypeInfo
{
    public function __construct(private readonly ?ReflectionType $type)
    {
    }

    public function isCompatible(mixed $value): bool
    {
        if ($this->type === null) {
            return true;
        }

        return $this->checkType($this->type, $value);
    }

    public function toString(): string
    {
        if ($this->type === null) {
            return '';
        }

        $type = $this->typeToString($this->type);

        return str_starts_with($type, '(')
            ? substr($type, 1, -1)
            : $type;
    }

    private function checkType(ReflectionType $type, mixed $value): bool
    {
        if ($type instanceof ReflectionUnionType) {
            return $this->checkUnionType($type, $value);
        }

        if ($type instanceof ReflectionIntersectionType) {
            return $this->checkIntersectionType($type, $value);
        }

        if ($type instanceof ReflectionNamedType) {
            return $this->checkNamedType($type, $value);
        }

        throw $this->makeUnexpectedReflectionTypeException($type);
    }

    private function checkUnionType(ReflectionUnionType $type, mixed $value): bool
    {
        foreach ($type->getTypes() as $innerType) {
            if ($this->checkType($innerType, $value)) {
                return true;
            }
        }

        return false;
    }

    private function checkIntersectionType(ReflectionIntersectionType $type, mixed $value): bool
    {
        foreach ($type->getTypes() as $innerType) {
            if (!$this->checkType($innerType, $value)) {
                return false;
            }
        }

        return true;
    }

    private function checkNamedType(ReflectionNamedType $type, mixed $value): bool
    {
        if ($value === null) {
            return $type->allowsNull();
        }

        $typeName = $type->getName();

        return match ($typeName) {
            'int' => is_int($value),
            'float' => is_float($value) || is_int($value),
            'string' => is_string($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            'object' => is_object($value),
            'callable' => is_callable($value),
            'iterable' => is_iterable($value),
            'mixed' => true,
            'null' => $value == null,
            'false' => $value === false,
            'true' => $value === true,
            default => $this->checkNamedClassType($typeName, $value),
        };
    }

    private function checkNamedClassType(string $typeName, mixed $value): bool
    {
        if (!is_object($value)) {
            return false;
        }

        return $value instanceof $typeName;
    }

    private function typeToString(ReflectionType $type): string
    {
        if ($type instanceof ReflectionUnionType) {
            return $this->compositeTypeToString('|', $type);
        }

        if ($type instanceof ReflectionIntersectionType) {
            return $this->compositeTypeToString('&', $type);
        }

        if ($type instanceof ReflectionNamedType) {
            return $this->namedTypeToString($type);
        }

        throw $this->makeUnexpectedReflectionTypeException($type);
    }

    private function compositeTypeToString(
        string $delimiter,
        ReflectionUnionType|ReflectionIntersectionType $type,
    ): string {
        $types = array_map(function (ReflectionType $type): string {
            return $this->typeToString($type);
        }, $type->getTypes());

        sort($types);

        return '(' . implode($delimiter, $types) . ')';
    }

    private function namedTypeToString(ReflectionNamedType $type): string
    {
        $name = $type->getName();

        if ($type->allowsNull() && ($name !== 'mixed' && $name !== 'null')) {
            return $name . '|null';
        }

        return $name;
    }

    private function makeUnexpectedReflectionTypeException(ReflectionType $type): LogicException
    {
        return new LogicException(sprintf('Unexpected reflection type: %s.', get_class($type)));
    }
}
