<?php
declare(strict_types=1);

namespace Pathway\Internal;

use Pathway\Internal\Support\TypeFormatter;
use Pathway\Internal\Throwable;

/**
 * @internal
 */
class Exception extends \Exception implements Throwable
{
    /**
     * @var array<string, mixed>
     */
    private readonly array $snapshot;

    public static function methodDoesNotExist(object $handler, string $method): self
    {
        return new self(ExceptionCode::METHOD_DOES_NOT_EXIST, [
            'code' => ExceptionCode::METHOD_DOES_NOT_EXIST,
            'handler' => $handler,
            'method' => $method,
        ]);
    }

    public static function methodNotPublicNonStatic(object $handler, string $method): self
    {
        return new self(ExceptionCode::METHOD_NOT_PUBLIC_NON_STATIC, [
            'code' => ExceptionCode::METHOD_NOT_PUBLIC_NON_STATIC,
            'handler' => $handler,
            'method' => $method,
        ]);
    }

    public static function mixedOrNonSequentialArguments(object $handler, string $method): self
    {
        return new self(ExceptionCode::MIXED_OR_NON_SEQUENTIAL_ARGUMENTS, [
            'code' => ExceptionCode::MIXED_OR_NON_SEQUENTIAL_ARGUMENTS,
            'handler' => $handler,
            'method' => $method,
        ]);
    }

    public static function missingArguments(object $handler, string $method): self
    {
        return new self(ExceptionCode::MISSING_ARGUMENTS, [
            'code' => ExceptionCode::MISSING_ARGUMENTS,
            'handler' => $handler,
            'method' => $method,
        ]);
    }

    public static function tooManyArguments(object $handler, string $method): self
    {
        return new self(ExceptionCode::TOO_MANY_ARGUMENTS, [
            'code' => ExceptionCode::TOO_MANY_ARGUMENTS,
            'handler' => $handler,
            'method' => $method,
        ]);
    }

    public static function eventHandlerNotObject(string $eventHandlerClassPath, int $index, mixed $handler): self
    {
        $type = TypeFormatter::format($handler);

        return new self(ExceptionCode::EVENT_HANDLER_NOT_OBJECT, [
            'code' => ExceptionCode::EVENT_HANDLER_NOT_OBJECT,
            'handler' => $eventHandlerClassPath,
            'index' => $index,
            'type' => $type,
        ]);
    }

    /**
     * @param array<string, mixed> $snapshot
     */
    public function __construct(ExceptionCode $code, array $snapshot)
    {
        parent::__construct(code: $code->value);

        $this->snapshot = $snapshot;
    }

    public function snapshot(): array
    {
        return $this->snapshot;
    }
}
