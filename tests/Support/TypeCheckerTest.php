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

    #[Test]
    #[DataProvider('provideObjectListValues')]
    public function it_verifies_object_lists(bool $expected, mixed $value): void
    {
        $this->assertSame($expected, TypeChecker::isObjectList($value));
    }

    public static function provideObjectListValues(): Generator
    {
        yield 'object list' => [
            'expected' => true,
            'value' => [
                new stdClass,
                new stdClass,
            ],
        ];

        yield 'mixed list (object and string)' => [
            'expected' => false,
            'value' => [
                new stdClass,
                'STRING',
            ],
        ];

        yield 'associative object array' => [
            'expected' => false,
            'value' => [
                'a' => new stdClass,
                'b' => new stdClass,
            ],
        ];

        yield 'string' => [
            'expected' => false,
            'value' => 'STRING',
        ];

        yield 'integer' => [
            'expected' => false,
            'value' => 42,
        ];

        yield 'null' => [
            'expected' => false,
            'value' => null,
        ];

        yield 'empty array' => [
            'expected' => true,
            'value' => [],
        ];
    }
}
