<?php
declare(strict_types=1);

namespace Tests\Support;

use Pathway\Internal\Support\TypeFormatter;

use Tests\Support\Fixtures\SimpleBackedEnum;
use Tests\Support\Fixtures\SimpleEnum;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Closure;

final class TypeFormatterTest extends TestCase
{
    #[Test]
    public function it_formats_null(): void
    {
        $this->assertSame('null', TypeFormatter::format(null));
    }

    #[Test]
    public function it_formats_true(): void
    {
        $this->assertSame('bool', TypeFormatter::format(true));
    }

    #[Test]
    public function it_formats_false(): void
    {
        $this->assertSame('bool', TypeFormatter::format(false));
    }

    #[Test]
    public function it_formats_integers(): void
    {
        $this->assertSame('int', TypeFormatter::format(123));
    }

    #[Test]
    public function it_formats_floats(): void
    {
        $this->assertSame('float', TypeFormatter::format(1.23));
    }

    #[Test]
    public function it_formats_strings(): void
    {
        $this->assertSame('string', TypeFormatter::format('STRING'));
    }

    #[Test]
    public function it_formats_arrays(): void
    {
        $this->assertSame('array', TypeFormatter::format([1, 2, 3]));
    }

    #[Test]
    public function it_formats_closures(): void
    {
        $closure = function () {
            return 42;
        };

        $this->assertSame('closure', TypeFormatter::format($closure));
    }

    #[Test]
    public function it_formats_enums(): void
    {
        $enum = SimpleEnum::ONE;

        $this->assertSame(
            'enum(Tests\Support\Fixtures\SimpleEnum)',
            TypeFormatter::format($enum),
        );
    }

    #[Test]
    public function it_formats_backed_enums(): void
    {
        $enum = SimpleBackedEnum::ONE;

        $this->assertSame(
            'enum(Tests\Support\Fixtures\SimpleBackedEnum)',
            TypeFormatter::format($enum),
        );
    }

    #[Test]
    public function it_formats_objects(): void
    {
        $obj = new class {
        };

        $this->assertSame(sprintf('object(%s)', get_class($obj)), TypeFormatter::format($obj));
    }
}
