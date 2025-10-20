<?php
declare(strict_types=1);

namespace Pathway\HandlerResolvers\InMemory;

use Pathway\Support\TypeFormatter;
use Pathway\Internal\Throwable;

use LogicException;

use function sprintf;

final class Exception extends LogicException implements Throwable
{
    /**
     * @internal
     */
    public static function noHandlerForCommand(string $classPath): self
    {
        return new self(sprintf(
            'No handler found for command "%s".',
            $classPath,
        ));
    }

    /**
     * @internal
     */
    public static function commandHandlerNotObject(string $classPath, mixed $handler): self
    {
        return new self(sprintf(
            'Handler for command "%s" must be an object, got %s.',
            $classPath,
            TypeFormatter::format($handler),
        ));
    }

    /**
     * @internal
     */
    public static function eventHandlerNotArray(string $classPath, mixed $handlers): self
    {
        return new self(sprintf(
            'Handler found for event "%s" must be an numeric array, got %s.',
            $classPath,
            TypeFormatter::format($handlers),
        ));
    }

    public static function eventHandlerArrayNotList(string $classPath): self
    {
        return new self(sprintf(
            'Handler found for event "%s" must be a numeric array.',
            $classPath,
        ));
    }

    /**
     * @internal
     */
    public static function eventHandlerNotObject(string $classPath, int $i, mixed $handler): self
    {
        return new self(sprintf(
            'Handler #%d for event "%s" must be an object, got %s.',
            $i,
            $classPath,
            TypeFormatter::format($handler),
        ));
    }

    /**
     * @internal
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function snapshot(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
