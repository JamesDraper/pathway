<?php
declare(strict_types=1);

namespace Tests\Internal;

use Pathway\Internal\TypeFormatter;

use Tests\Internal\Fixtures\SimpleBackedEnum;
use Tests\Internal\Fixtures\SimpleEnum;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Generator;

final class TypeFormatterTest extends TestCase
{
    #[Test]
    #[DataProvider('provideValues')]
    public function it_formats_correctly(string $expected, mixed $value): void
    {
        $typeFormatter = new TypeFormatter;

        $this->assertSame($expected, $typeFormatter->format($value));
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
            'expected' => 'enum(Tests\Internal\Fixtures\SimpleEnum)',
            'value' => SimpleEnum::ONE,
        ];

        yield 'backed enum' => [
            'expected' => 'enum(Tests\Internal\Fixtures\SimpleBackedEnum)',
            'value' => SimpleBackedEnum::ONE,
        ];
    }

    #[Test]
    public function it_formats_closures_correctly(): void
    {
        $typeFormatter = new TypeFormatter;

        $closure = function () {
            return 42;
        };

        $this->assertSame('closure', $typeFormatter->format($closure));
    }

    #[Test]
    public function it_formats_objects_correctly(): void
    {
        $typeFormatter = new TypeFormatter;

        $obj = new class {
        };

        $this->assertSame(
            sprintf('object(%s)', get_class($obj)),
            $typeFormatter->format($obj),
        );
    }
}
