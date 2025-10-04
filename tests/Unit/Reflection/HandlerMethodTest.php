<?php
declare(strict_types=1);

namespace Tests\Unit\Reflection;

use Pathway\Reflection\HandlerMethod;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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

        $method = new HandlerMethod($handler, 'add');

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

        $method = new HandlerMethod($handler, 'increment');

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

        $method = new HandlerMethod($handler, 'add');

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

        $method = new HandlerMethod($handler, 'add');

        $this->assertSame(3, $method(['a' => 1, 'b' => 2]));
        $this->assertSame(7, $method(['a' => 3, 'b' => 4]));
    }


    #[Test]
    public function itThrowsAnExceptionWhenArgumentMissingAndNoDefault(): void
    {
        $handler = new class {
            public function increment(int $num): string
            {
                return $num + 1;
            }
        };

        $method = new HandlerMethod($handler, 'increment');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('missing-argument');

        $method([]);
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodIsStatic(): void
    {
        $handler = new class {
            public static function bad(): void {}
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('~must be public and non-static~');

        new HandlerMethod($handler, 'bad');
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodIsProtected(): void
    {
        $handler = new class {
            protected function secret(): void {}
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('~must be public and non-static~');

        new HandlerMethod($handler, 'secret');
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodIsPrivate(): void
    {
        $handler = new class {
            private function secret(): void {}
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('~must be public and non-static~');

        new HandlerMethod($handler, 'secret');
    }

    #[Test]
    public function itThrowsAnExceptionWhenMethodDoesNotExist(): void
    {
        $handler = new class {
            public function real(): void {}
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('method does not exist.');

        new HandlerMethod($handler, 'fakeMethod');
    }

    #[Test]
    public function itCanBeInvokedWithBasicPositionalArguments(): void
    {
        $handler = new class {
            public function handle(string $a, string $b): array
            {
                return [$a, $b];
            }
        };

        $method = new HandlerMethod($handler, 'handle');
        $this->assertSame(['one', 'two'], $method(['one', 'two']));
    }

    #[Test]
    public function itThrowsAnExceptionWhenMissingRequiredPositionalArgument(): void
    {
        $handler = new class {
            public function handle(string $a, string $b): void {}
        };

        $method = new HandlerMethod($handler, 'handle');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('missing-argument');

        $method(['only-one']);
    }

    #[Test]
    public function itThrowsAnExceptionWhenTooManyPositionalArguments(): void
    {
        $handler = new class {
            public function handle(string $a): void {}
        };

        $method = new HandlerMethod($handler, 'handle');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('too-many-args');

        $method(['one', 'two']);
    }

    #[Test]
    public function itUsesDefaultWhenPositionalArgumentMissing(): void
    {
        $handler = new class {
            public function handle(string $a, string $b = 'default'): array
            {
                return [$a, $b];
            }
        };

        $method = new HandlerMethod($handler, 'handle');
        $this->assertSame(['one', 'default'], $method(['one']));
    }

    #[Test]
    public function itCanBeInvokedWithVariadicPositionalArguments(): void
    {
        $handler = new class {
            public function handle(string $a, string ...$rest): array
            {
                return array_merge([$a], $rest);
            }
        };

        $method = new HandlerMethod($handler, 'handle');
        $this->assertSame(['one', 'two', 'three'], $method(['one', 'two', 'three']));
    }

    #[Test]
    public function itCanBeInvokedWithEmptyVariadicPositionalArguments(): void
    {
        $handler = new class {
            public function handle(string $a, string ...$rest): array
            {
                return array_merge([$a], $rest);
            }
        };

        $method = new HandlerMethod($handler, 'handle');
        $this->assertSame(['one'], $method(['one']));
    }

    #[Test]
    public function itCanBeInvokedWithDefaultAndVariadicCombination(): void
    {
        $handler = new class {
            public function handle(string $a, string $b = 'default', string ...$rest): array
            {
                return array_merge([$a, $b], $rest);
            }
        };

        $method = new HandlerMethod($handler, 'handle');

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

        $method = new HandlerMethod($handler, 'handle');
        $this->assertSame('ok', $method([]));
    }

    #[Test]
    public function itCanBeInvokedWithOnlyVariadicParameters(): void
    {
        $handler = new class {
            public function handle(string ...$args): array
            {
                return $args;
            }
        };

        $method = new HandlerMethod($handler, 'handle');

        $this->assertSame([], $method([]));
        $this->assertSame(['a', 'b'], $method(['a', 'b']));
    }
}
