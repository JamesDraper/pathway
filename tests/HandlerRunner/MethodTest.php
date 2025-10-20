<?php
declare(strict_types=1);

namespace Tests\HandlerRunner;

use Pathway\Internal\HandlerRunner\Method;
use Pathway\Internal\Exception;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use ReflectionClass;

final class MethodTest extends TestCase
{
    private readonly object $addHandler;

    private readonly object $toArrayHandler;

    /**
     * @phpstan-ignore missingType.generics
     */
    private readonly Method $addMethod;

    /**
     * @phpstan-ignore missingType.generics
     */
    private readonly Method $toArrayMethod;

    #[Test]
    public function it_can_be_invoked_without_arguments(): void
    {
        $handler = new class {
            public function five(): int
            {
                return 5;
            }
        };

        $method = $this->makeMethod($handler, 'five');

        $result = $method->invoke([]);

        $this->assertSame(5, $result);
    }

    #[Test]
    public function it_can_be_invoked_with_named_arguments(): void
    {
        $result = $this->addMethod->invoke(['a' => 1, 'b' => 2]);

        $this->assertSame(3, $result);
    }

    #[Test]
    public function it_can_be_invoked_with_positional_arguments(): void
    {
        $result = $this->addMethod->invoke([1, 2]);

        $this->assertSame(3, $result);
    }

    #[Test]
    public function it_can_be_invoked_multiple_times_with_named_arguments(): void
    {
        $result1 = $this->addMethod->invoke(['a' => 1, 'b' => 2]);
        $result2 = $this->addMethod->invoke(['a' => 3, 'b' => 4]);

        $this->assertSame(3, $result1);
        $this->assertSame(7, $result2);
    }

    #[Test]
    public function it_can_be_invoked_multiple_times_with_positional_arguments(): void
    {
        $result1 = $this->addMethod->invoke([1, 2]);
        $result2 = $this->addMethod->invoke([3, 4]);

        $this->assertSame(3, $result1);
        $this->assertSame(7, $result2);
    }

    #[Test]
    public function it_can_be_invoked_with_default_arguments(): void
    {
        $result = $this->addMethod->invoke([]);

        $this->assertSame(3, $result);
    }

    #[Test]
    public function it_can_be_invoked_with_partial_named_arguments_and_defaults(): void
    {
        $result = $this->addMethod->invoke(['a' => 3]);

        $this->assertSame(5, $result);
    }

    #[Test]
    public function it_can_be_invoked_with_partial_positional_arguments_and_defaults(): void
    {
        $result = $this->addMethod->invoke([3]);

        $this->assertSame(5, $result);
    }

    #[Test]
    public function it_can_be_invoked_with_variadic_positional_arguments(): void
    {
        $result = $this->toArrayMethod->invoke([1, 2, 3, 4]);

        $this->assertSame([1, 2, 3, 4], $result);
    }

    #[Test]
    public function it_can_be_invoked_with_empty_variadic_and_all_positional_arguments(): void
    {
        $result = $this->toArrayMethod->invoke([1, 2]);

        $this->assertSame([1, 2], $result);
    }

    #[Test]
    public function it_can_be_invoked_with_empty_variadic_and_some_positional_arguments_with_defaults(): void
    {
        $result = $this->toArrayMethod->invoke([1]);

        $this->assertSame([1, 2], $result);
    }

    #[Test]
    public function it_can_be_invoked_with_only_variadic_positional_arguments(): void
    {
        $handler = new class {
            /**
             * @return int[]
             */
            public function toArray(int ...$ints): array
            {
                return [...$ints];
            }
        };

        $method = $this->makeMethod($handler, 'toArray');

        $this->assertSame([1, 2, 3], $method->invoke([1, 2, 3]));
    }

    #[Test]
    public function it_throws_an_exception_when_argument_missing_and_no_default(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $method = $this->makeMethod($handler, 'add');

        $this->assertThrown(
            Exception::missingArguments($handler, 'add'),
            static fn () => $method->invoke([]),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_is_static(): void
    {
        $handler = new class {
            public static function add(int $a = 1, int $b = 2): int
            {
                return $a + $b;
            }
        };

        $this->assertThrown(
            Exception::methodNotPublicNonStatic($handler, 'add'),
            fn () => $this->makeMethod($handler, 'add'),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_is_protected(): void
    {
        $handler = new class {
            protected function add(int $a = 1, int $b = 2): int
            {
                return $a + $b;
            }
        };

        $this->assertThrown(
            Exception::methodNotPublicNonStatic($handler, 'add'),
            fn () => $this->makeMethod($handler, 'add'),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_is_private(): void
    {
        $handler = new class {
            /**
             * @phpstan-ignore method.unused
             */
            private function add(int $a = 1, int $b = 2): int
            {
                return $a + $b;
            }
        };

        $this->assertThrown(
            Exception::methodNotPublicNonStatic($handler, 'add'),
            fn () => $this->makeMethod($handler, 'add'),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_method_does_not_exist(): void
    {
        $handler = new class {
            /**
             * @phpstan-ignore method.unused
             */
            private function subtract(int $a = 1, int $b = 2): int
            {
                return $a - $b;
            }
        };

        $this->assertThrown(
            Exception::methodDoesNotExist($handler, 'add'),
            fn () => $this->makeMethod($handler, 'add'),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_missing_a_required_named_argument(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $method = $this->makeMethod($handler, 'add');

        $this->assertThrown(
            Exception::missingArguments($handler, 'add'),
            static fn () => $method->invoke(['a' => 1]),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_missing_a_required_positional_argument(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $method = $this->makeMethod($handler, 'add');

        $this->assertThrown(
            Exception::missingArguments($handler, 'add'),
            static fn () => $method->invoke([1]),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_too_many_named_arguments(): void
    {
        $this->assertThrown(
            Exception::tooManyArguments($this->addHandler, 'add'),
            fn () => $this->addMethod->invoke(['a' => 1, 'b' => 2, 'c' => 3]),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_too_many_positional_arguments(): void
    {
        $this->assertThrown(
            Exception::tooManyArguments($this->addHandler, 'add'),
            fn () => $this->addMethod->invoke([1, 2, 3]),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_invoked_with_non_zero_indexed_list(): void
    {
        $this->assertThrown(
            Exception::mixedOrNonSequentialArguments($this->addHandler, 'add'),
            fn () => $this->addMethod->invoke([1 => 1, 2 => 2]),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_invoked_with_mixed_numeric_and_string_keys(): void
    {
        $this->assertThrown(
            Exception::mixedOrNonSequentialArguments($this->addHandler, 'add'),
            fn () => $this->addMethod->invoke([2, 'b' => 3]),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->addHandler = new class {
            public function add(int $a = 1, int $b = 2): int
            {
                return $a + $b;
            }
        };

        $this->toArrayHandler = new class {
            /**
             * @return int[]
             */
            public function toArray(int $a = 1, int $b = 2, int ...$c): array
            {
                return [$a, $b, ...$c];
            }
        };

        $this->addMethod = $this->makeMethod($this->addHandler, 'add');
        $this->toArrayMethod = $this->makeMethod($this->toArrayHandler, 'toArray');
    }

    /**
     * @template THandler of object
     * @param THandler $handler
     * @return Method<THandler>
     */
    private function makeMethod(object $handler, string $method): Method
    {
        return new Method(new ReflectionClass($handler), $handler, $method);
    }
}
