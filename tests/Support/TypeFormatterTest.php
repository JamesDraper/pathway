<?php
declare(strict_types=1);

namespace Tests\Support;

use Pathway\Internal\Support\TypeFormatter;

use Tests\Support\Fixtures\SimpleBackedEnum;
use Tests\Support\Fixtures\SimpleEnum;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Generator;
use Closure;

final class TypeFormatterTest extends TestCase
{
    #[Test]
    #[DataProvider('provideValues')]
    public function it_formats_correctly(string $expected, mixed $value): void
    {
        $this->assertSame($expected, TypeFormatter::format($value));
    }

    public static function provideValues(): Generator
    {
        yield 'null' => [
            'expected' => 'null',
            'value' => null,
        ];

        yield 'true' => [
            'expected' => 'bool',
            'value' => true,
        ];

        yield 'false' => [
            'expected' => 'bool',
            'value' => false,
        ];

        yield 'integer' => [
            'expected' => 'int',
            'value' => 123,
        ];

        yield 'float' => [
            'expected' => 'float',
            'value' => 1.23,
        ];

        yield 'string' => [
            'expected' => 'string',
            'value' => 'STRING',
        ];

        yield 'array' => [
            'expected' => 'array',
            'value' => [1, 2, 3],
        ];

        yield 'enum' => [
            'expected' => 'enum(Tests\Support\Fixtures\SimpleEnum)',
            'value' => SimpleEnum::ONE,
        ];

        yield 'backed enum' => [
            'expected' => 'enum(Tests\Support\Fixtures\SimpleBackedEnum)',
            'value' => SimpleBackedEnum::ONE,
        ];
    }

    #[Test]
    public function it_formats_closures_correctly(): void
    {
        $closure = function () {
            return 42;
        };

        $this->assertSame('closure', TypeFormatter::format($closure));
    }

    #[Test]
    public function it_formats_objects_correctly(): void
    {
        $obj = new class {
        };

        $this->assertSame(
            sprintf('object(%s)', get_class($obj)),
            TypeFormatter::format($obj),
        );
    }
}
