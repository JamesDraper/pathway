<?php
declare(strict_types=1);

namespace Tests\Support;

use Pathway\Internal\Support\TypeChecker;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Generator;
use stdClass;

final class TypeCheckerTest extends TestCase
{
    #[Test]
    #[DataProvider('provideArrayValues')]
    public function it_verifies_arrays(bool $expected, mixed $value): void
    {
        self::assertSame($expected, TypeChecker::isArray($value));
    }

    public static function provideArrayValues(): Generator
    {
        yield 'empty array' => [
            'expected' => true,
            'value' => [],
        ];

        yield 'indexed array' => [
            'expected' => true,
            'value' => ['a', 'b'],
        ];

        yield 'string' => [
            'expected' => false,
            'value' => 'STRING',
        ];

        yield 'null' => [
            'expected' => false,
            'value' => null,
        ];

        yield 'object' => [
            'expected' => false,
            'value' => new stdClass,
        ];
    }

    #[Test]
    #[DataProvider('provideObjectValues')]
    public function it_verifies_objects(bool $expected, mixed $value): void
    {
        $this->assertSame($expected, TypeChecker::isObject($value));
    }

    public static function provideObjectValues(): Generator
    {
        yield 'stdClass' => [
            'expected' => true,
            'value' => new stdClass,
        ];

        yield 'string' => [
            'expected' => false,
            'value' => 'string',
        ];

        yield 'int' => [
            'expected' => false,
            'value' => 42,
        ];

        yield 'null' => [
            'expected' => false,
            'value' => null,
        ];
    }

    /**
     * @param array<int|string, mixed> $value
     */
    #[Test]
    #[DataProvider('provideAssociativeArrayValues')]
    public function it_verifies_associative_arrays(bool $expected, array $value): void
    {
        $this->assertSame($expected, TypeChecker::arrayIsAssociative($value));
    }

    public static function provideAssociativeArrayValues(): Generator
    {
        yield 'list array' => [
            'expected' => false,
            'value' => [1, 2, 3],
        ];

        yield 'associative array' => [
            'expected' => true,
            'value' => [
                'a' => 1,
                'b' => 2,
            ],
        ];

        yield 'empty array' => [
            'expected' => true,
            'value' => [],
        ];

        yield 'numeric string keys' => [
            'expected' => false,
            'value' => [
                '0' => 'a',
                '1' => 'b',
            ],
        ];

        yield 'mixed keys' => [
            'expected' => false,
            'value' => [
                0 => 'a',
                'two' => 'b',
            ],
        ];
    }

    /**
     * @param array<int|string, mixed> $value
     */
    #[Test]
    #[DataProvider('provideArrayListValues')]
    public function it_verifies_lists(bool $expected, array $value): void
    {
        $this->assertSame($expected, TypeChecker::arrayIsList($value));
    }

    public static function provideArrayListValues(): Generator
    {
        yield 'list array' => [
            'expected' => true,
            'value' => [1, 2, 3],
        ];

        yield 'associative array' => [
            'expected' => false,
            'value' => ['a' => 1, 'b' => 2],
        ];

        yield 'empty array' => [
            'expected' => true,
            'value' => [],
        ];

        yield 'numeric string keys' => [
            'expected' => true,
            'value' => ['0' => 'a', '1' => 'b'],
        ];

        yield 'mixed keys' => [
            'expected' => false,
            'value' => [0 => 'a', 'two' => 'b'],
        ];
    }
}
