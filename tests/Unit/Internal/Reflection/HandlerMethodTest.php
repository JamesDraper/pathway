<?php
declare(strict_types=1);

namespace Tests\Unit\Internal\Reflection;

use Pathway\Internal\Exceptions\ReflectionException;
use Pathway\Internal\Reflection\HandlerMethod;
use Pathway\Internal\Reflection\Exception;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use ReflectionClass;

final class HandlerMethodTest extends TestCase
{
    #[Test]
    public function it_can_be_invoked_with_named_arguments(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $result = $method->invoke(['a' => 1, 'b' => 2]);

        $this->assertSame(3, $result);
    }

    #[Test]
    public function it_can_be_invoked_with_default_arguments(): void
    {
        $handler = new class {
            public function increment(int $num = 1): int
            {
                return $num + 1;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'increment');

        $result = $method->invoke([]);

        $this->assertSame(2, $result);
    }

    #[Test]
    public function it_can_be_invoked_with_partial_arguments_and_defaults(): void
    {
        $handler = new class {
            public function add(int $a, int $b = 1): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $result = $method->invoke(['a' => 3]);

        $this->assertSame(4, $result);
    }

    #[Test]
    public function it_can_be_invoked_multiple_times(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $this->assertSame(3, $method->invoke(['a' => 1, 'b' => 2]));
        $this->assertSame(7, $method->invoke(['a' => 3, 'b' => 4]));
    }


    #[Test]
    public function it_throws_an_exception_when_argument_missing_and_no_default(): void
    {
        $handler = new class {
            public function increment(int $num): int
            {
                return $num + 1;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'increment');

        $this->assertThrown(
            ReflectionException::missingArguments($handler, 'increment'),
            static fn () => $method->invoke([]),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_is_static(): void
    {
        $handler = new class {
            public static function bad(): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->assertThrown(
            ReflectionException::methodNotPublicNonStatic($handler, 'bad'),
            static fn () => new HandlerMethod($reflectionClass, $handler, 'bad'),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_is_protected(): void
    {
        $handler = new class {
            protected function secret(): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->assertThrown(
            ReflectionException::methodNotPublicNonStatic($handler, 'secret'),
            static fn () => new HandlerMethod($reflectionClass, $handler, 'secret'),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_is_private(): void
    {
        $handler = new class {
            private function secret(): void // @phpstan-ignore method.unused
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->assertThrown(
            ReflectionException::methodNotPublicNonStatic($handler, 'secret'),
            static fn () => new HandlerMethod($reflectionClass, $handler, 'secret'),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_does_not_exist(): void
    {
        $handler = new class {
            public function real(): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->assertThrown(
            ReflectionException::methodDoesNotExist($handler, 'handle'),
            static fn () => new HandlerMethod($reflectionClass, $handler, 'handle'),
        );
    }

    #[Test]
    public function it_can_be_invoked_with_basic_positional_arguments(): void
    {
        $handler = new class {
            /**
             * @return array{string, string}
             */
            public function handle(string $a, string $b): array
            {
                return [$a, $b];
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');
        $this->assertSame(['one', 'two'], $method->invoke(['one', 'two']));
    }

    #[Test]
    public function it_throws_an_exception_when_missing_required_positional_argument(): void
    {
        $handler = new class {
            public function handle(string $a, string $b): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');

        $this->assertThrown(
            ReflectionException::missingArguments($handler, 'handle'),
            static fn () => $method->invoke(['only-one']),
        );
    }

    #[Test]
    public function it_throws_an_exception_whenT_too_many_positional_arguments(): void
    {
        $handler = new class {
            public function handle(string $a): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');

        $this->assertThrown(
            ReflectionException::tooManyArguments($handler, 'handle'),
            static fn () => $method->invoke(['one', 'two']),
        );
    }

    #[Test]
    public function it_uses_default_when_positional_argument_missing(): void
    {
        $handler = new class {
            /**
             * @return array{string, string}
             */
            public function handle(string $a, string $b = 'default'): array
            {
                return [$a, $b];
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');
        $this->assertSame(['one', 'default'], $method->invoke(['one']));
    }

    #[Test]
    public function it_can_be_invoked_with_variadic_positional_arguments(): void
    {
        $handler = new class {
            /**
             * @return string[]
             */
            public function handle(string $a, string ...$rest): array
            {
                return array_merge([$a], $rest);
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');
        $this->assertSame(['one', 'two', 'three'], $method->invoke(['one', 'two', 'three']));
    }

    #[Test]
    public function it_can_be_invoked_with_empty_variadic_positional_arguments(): void
    {
        $handler = new class {
            /**
             * @return string[]
             */
            public function handle(string $a, string ...$rest): array
            {
                return array_merge([$a], $rest);
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');
        $this->assertSame(['one'], $method->invoke(['one']));
    }

    #[Test]
    public function it_can_be_invoked_with_default_and_variadic_combination(): void
    {
        $handler = new class {
            /**
             * @return string[]
             */
            public function handle(string $a, string $b = 'default', string ...$rest): array
            {
                return array_merge([$a, $b], $rest);
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');

        $this->assertSame(['one', 'two', 'three', 'four'], $method->invoke(['one', 'two', 'three', 'four']));
        $this->assertSame(['one', 'default'], $method->invoke(['one']));
    }

    #[Test]
    public function it_can_be_invoked_with_empty_parameter_list(): void
    {
        $handler = new class {
            public function handle(): string
            {
                return 'ok';
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');
        $this->assertSame('ok', $method->invoke([]));
    }

    #[Test]
    public function it_can_be_invoked_with_only_variadic_parameters(): void
    {
        $handler = new class {
            /**
             * @return string[]
             */
            public function handle(string ...$args): array
            {
                return $args;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');

        $this->assertSame([], $method->invoke([]));
        $this->assertSame(['a', 'b'], $method->invoke(['a', 'b']));
    }

    #[Test]
    public function it_throws_an_exception_when_invoked_with_non_zero_indexed_list(): void
    {
        $handler = new class {
            public function run(string $a, string $b): string
            {
                return $a . $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'run');

        $this->assertThrown(
            ReflectionException::mixedOrNonSequentialArguments($handler, 'run'),
            static fn () => $method->invoke([1 => 'x', 2 => 'y']),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_invoked_with_mixed_numeric_and_string_keys(): void
    {
        $handler = new class {
            public function mix(string $a, string $b): string
            {
                return $a . $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'mix');

        $this->assertThrown(
            ReflectionException::mixedOrNonSequentialArguments($handler, 'mix'),
            static fn () => $method->invoke(['a' => 'foo', 0 => 'bar']),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_invoke_with_too_many_positional_arguments(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $this->assertThrown(
            ReflectionException::tooManyArguments($handler, 'add'),
            static fn () => $method->invoke([1, 2, 3]),
        );
    }
}
