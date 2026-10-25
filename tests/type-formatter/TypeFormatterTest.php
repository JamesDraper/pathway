<?php

declare(strict_types=1);

namespace Tests\TypeFormatter;

use Pathway\Internal\TypeFormatter;

use Tests\TypeFormatter\Fixtures\SimpleBackedEnum;
use Tests\TypeFormatter\Fixtures\SimpleEnum;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function get_class;
use function sprintf;
use function tmpfile;
use function fclose;

final class TypeFormatterTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(TypeFormatter::class);
    }

    #[Test]
    #[DataProvider('provider_it_formats')]
    public function it_formats(string $expected, mixed $value): void
    {
        $typeFormatter = new TypeFormatter();

        $this->assertSame($expected, $typeFormatter->format($value));
    }

    public static function provider_it_formats(): array
    {
        return [
            'null' => [
                'expected' => 'null',
                'value' => null,
            ],
            'true' => [
                'expected' => 'bool',
                'value' => true,
            ],
            'false' => [
                'expected' => 'bool',
                'value' => false,
            ],
            'integer' => [
                'expected' => 'int',
                'value' => 123,
            ],
            'float' => [
                'expected' => 'float',
                'value' => 1.23,
            ],
            'string' => [
                'expected' => 'string',
                'value' => 'STRING',
            ],
            'array' => [
                'expected' => 'array',
                'value' => [1, 2, 3],
            ],
            'enum' => [
                'expected' => 'enum(Tests\\TypeFormatter\\Fixtures\\SimpleEnum)',
                'value' => SimpleEnum::ONE,
            ],
            'backed enum' => [
                'expected' => 'enum(Tests\\TypeFormatter\\Fixtures\\SimpleBackedEnum)',
                'value' => SimpleBackedEnum::ONE,
            ],
        ];
    }

    #[Test]
    public function it_formats_closures(): void
    {
        $typeFormatter = new TypeFormatter();

        $closure = static fn() => 123;

        $this->assertSame('closure', $typeFormatter->format($closure));
    }

    #[Test]
    public function it_formats_objects(): void
    {
        $typeFormatter = new TypeFormatter();

        $obj = new class {};

        $this->assertSame(
            sprintf('object(%s)', get_class($obj)),
            $typeFormatter->format($obj),
        );
    }

    #[Test]
    public function it_formats_resources(): void
    {
        $typeFormatter = new TypeFormatter();

        $file = tmpfile();

        try {
            $this->assertSame('resource', $typeFormatter->format($file));
        } finally {
            fclose($file);
        }
    }

    #[Test]
    public function it_formats_closed_resources(): void
    {
        $typeFormatter = new TypeFormatter();

        $file = tmpfile();

        fclose($file);

        $this->assertSame('resource', $typeFormatter->format($file));
    }
}
