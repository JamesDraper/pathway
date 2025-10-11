<?php
declare(strict_types=1);

namespace Pathway\Internal\Exceptions;

/**
 * @internal
 */
enum ReflectionExceptionCode: int
{
    case METHOD_DOES_NOT_EXIST = 0;
    case METHOD_NOT_PUBLIC_NON_STATIC = 1;
    case MIXED_OR_NON_SEQUENTIAL_ARGUMENTS = 2;
    case MISSING_ARGUMENTS = 3;
    case TOO_MANY_ARGUMENTS = 4;
}
