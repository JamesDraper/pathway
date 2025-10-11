<?php
declare(strict_types=1);

namespace Pathway\Internal\Exceptions;

use Pathway\Throwable;

use LogicException;

/**
 * @internal
 */
class HandlerException extends LogicException implements Throwable
{
    private readonly object $handler;

    private readonly string $methodName;

    public static function methodDoesNotExist(object $handler, string $methodName): self
    {
        return new self($handler, $methodName, HandlerExceptionCode::METHOD_DOES_NOT_EXIST);
    }

    public static function methodNotPublicNonStatic(object $handler, string $methodName): self
    {
        return new self($handler, $methodName, HandlerExceptionCode::METHOD_NOT_PUBLIC_NON_STATIC);
    }

    public static function mixedOrNonSequentialArguments(object $handler, string $methodName): self
    {
        return new self($handler, $methodName, HandlerExceptionCode::MIXED_OR_NON_SEQUENTIAL_ARGUMENTS);
    }

    public static function missingArguments(object $handler, string $methodName): self
    {
        return new self($handler, $methodName, HandlerExceptionCode::MISSING_ARGUMENTS);
    }

    public static function tooManyArguments(object $handler, string $methodName): self
    {
        return new self($handler, $methodName, HandlerExceptionCode::TOO_MANY_ARGUMENTS);
    }

    public function __construct(object $handler, string $methodName, HandlerExceptionCode $code)
    {
        parent::__construct(code: $code->value);

        $this->handler = $handler;
        $this->methodName = $methodName;
    }

    public function snapshot(): array
    {
        return [
            'code' => $this->getCode(),
            'handler' => $this->handler::class,
            'methodName' => $this->methodName,
        ];
    }
}
