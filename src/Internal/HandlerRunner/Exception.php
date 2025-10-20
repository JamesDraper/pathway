<?php
declare(strict_types=1);

namespace Pathway\Internal\HandlerRunner;

use Pathway\Internal\Throwable;

use LogicException;

/**
 * @internal
 */
class Exception extends LogicException implements Throwable
{
    private readonly object $handler;

    private readonly string $method;

    public static function methodDoesNotExist(object $handler, string $method): self
    {
        return new self($handler, $method, ExceptionCode::METHOD_DOES_NOT_EXIST);
    }

    public static function methodNotPublicNonStatic(object $handler, string $method): self
    {
        return new self($handler, $method, ExceptionCode::METHOD_NOT_PUBLIC_NON_STATIC);
    }

    public static function mixedOrNonSequentialArguments(object $handler, string $method): self
    {
        return new self($handler, $method, ExceptionCode::MIXED_OR_NON_SEQUENTIAL_ARGUMENTS);
    }

    public static function missingArguments(object $handler, string $method): self
    {
        return new self($handler, $method, ExceptionCode::MISSING_ARGUMENTS);
    }

    public static function tooManyArguments(object $handler, string $method): self
    {
        return new self($handler, $method, ExceptionCode::TOO_MANY_ARGUMENTS);
    }

    public function __construct(object $handler, string $method, ExceptionCode $code)
    {
        parent::__construct(code: $code->value);

        $this->handler = $handler;
        $this->method = $method;
    }

    public function snapshot(): array
    {
        return [
            'code' => $this->getCode(),
            'handler' => $this->handler::class,
            'method' => $this->method,
        ];
    }
}
