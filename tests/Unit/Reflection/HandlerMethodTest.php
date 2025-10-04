<?php
declare(strict_types=1);

namespace Tests\Unit\Reflection;

use Pathway\Reflection\HandlerMethod;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use ReflectionClass;
use LogicException;

final class HandlerMethodTest extends TestCase
{
    #[Test]
    public function itCanBeInvokedWithNamedArguments(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $result = $method(['a' => 1, 'b' => 2]);

        $this->assertSame(3, $result);
    }

    #[Test]
    public function itCanBeInvokedWithDefaultArguments(): void
    {
        $handler = new class {
            public function increment(int $num = 1): int
            {
                return $num + 1;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'increment');

        $result = $method([]);

        $this->assertSame(2, $result);
    }

    #[Test]
    public function itCanBeInvokedWithPartialArgumentsAndDefaults(): void
    {
        $handler = new class {
            public function add(int $a, int $b = 1): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $result = $method(['a' => 3]);

        $this->assertSame(4, $result);
    }

    #[Test]
    public function itCanBeInvokedMultipleTimes(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $this->assertSame(3, $method(['a' => 1, 'b' => 2]));
        $this->assertSame(7, $method(['a' => 3, 'b' => 4]));
    }


    #[Test]
    public function itThrowsAnExceptionWhenArgumentMissingAndNoDefault(): void
    {
        $handler = new class {
            public function increment(int $num): int
            {
                return $num + 1;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'increment');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('missing-argument');

        $method([]);
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodIsStatic(): void
    {
        $handler = new class {
            public static function bad(): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('~must be public and non-static~');

        new HandlerMethod($reflectionClass, $handler, 'bad');
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodIsProtected(): void
    {
        $handler = new class {
            protected function secret(): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('~must be public and non-static~');

        new HandlerMethod($reflectionClass, $handler, 'secret');
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodIsPrivate(): void
    {
        $handler = new class {
            private function secret(): void // @phpstan-ignore method.unused
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('~must be public and non-static~');

        new HandlerMethod($reflectionClass, $handler, 'secret');
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodDoesNotExist(): void
    {
        $handler = new class {
            public function real(): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('method does not exist.');

        new HandlerMethod($reflectionClass, $handler, 'fakeMethod');
    }

    #[Test]
    public function itCanBeInvokedWithBasicPositionalArguments(): void
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
        $this->assertSame(['one', 'two'], $method(['one', 'two']));
    }

    #[Test]
    public function itThrowsAnExceptionWhenMissingRequiredPositionalArgument(): void
    {
        $handler = new class {
            public function handle(string $a, string $b): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('missing-argument');

        $method(['only-one']);
    }

    #[Test]
    public function itThrowsAnExceptionWhenTooManyPositionalArguments(): void
    {
        $handler = new class {
            public function handle(string $a): void
            {
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('too-many-args');

        $method(['one', 'two']);
    }

    #[Test]
    public function itUsesDefaultWhenPositionalArgumentMissing(): void
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
        $this->assertSame(['one', 'default'], $method(['one']));
    }

    #[Test]
    public function itCanBeInvokedWithVariadicPositionalArguments(): void
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
        $this->assertSame(['one', 'two', 'three'], $method(['one', 'two', 'three']));
    }

    #[Test]
    public function itCanBeInvokedWithEmptyVariadicPositionalArguments(): void
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
        $this->assertSame(['one'], $method(['one']));
    }

    #[Test]
    public function itCanBeInvokedWithDefaultAndVariadicCombination(): void
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

        $this->assertSame(['one', 'two', 'three', 'four'], $method(['one', 'two', 'three', 'four']));
        $this->assertSame(['one', 'default'], $method(['one']));
    }

    #[Test]
    public function itCanBeInvokedWithEmptyParameterList(): void
    {
        $handler = new class {
            public function handle(): string
            {
                return 'ok';
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'handle');
        $this->assertSame('ok', $method([]));
    }

    #[Test]
    public function itCanBeInvokedWithOnlyVariadicParameters(): void
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

        $this->assertSame([], $method([]));
        $this->assertSame(['a', 'b'], $method(['a', 'b']));
    }

    #[Test]
    public function itThrowsAnExceptionWhenInvokedAWithNonZeroIndexedList(): void
    {
        $handler = new class {
            public function run(string $a, string $b): string
            {
                return $a . $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'run');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('invalid-arguments');

        $method([1 => 'x', 2 => 'y']);
    }

    #[Test]
    public function itThrowsAnExceptionWhenInvokedWithMixedNumericAndStringKeys(): void
    {
        $handler = new class {
            public function mix(string $a, string $b): string
            {
                return $a . $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'mix');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('invalid-arguments');

        $method(['a' => 'foo', 0 => 'bar']);
    }

    #[Test]
    public function itThrowsAnExceptionWhenInvokeWithTooManyPositionalArguments(): void
    {
        $handler = new class {
            public function add(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $reflectionClass = new ReflectionClass($handler);

        $method = new HandlerMethod($reflectionClass, $handler, 'add');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('too-many-args');

        $method([1, 2, 3]); // extra argument beyond expected
    }
}
