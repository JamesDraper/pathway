<?php
declare(strict_types=1);

namespace Pathway\Tests\Unit\Internal;

use Pathway\Internal\TypeFormatter;

use Tests\Fixtures\SimpleBackedEnum;
use Tests\Fixtures\SimpleEnum;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Closure;

final class TypeFormatterTest extends TestCase
{
    private TypeFormatter $formatter;

    #[Test]
    public function it_formats_null(): void
    {
        $this->assertSame('null', $this->formatter->format(null));
    }

    #[Test]
    public function it_formats_true(): void
    {
        $this->assertSame('bool', $this->formatter->format(true));
    }

    #[Test]
    public function it_formats_false(): void
    {
        $this->assertSame('bool', $this->formatter->format(false));
    }

    #[Test]
    public function it_formats_integers(): void
    {
        $this->assertSame('int', $this->formatter->format(123));
    }

    #[Test]
    public function it_formats_floats(): void
    {
        $this->assertSame('float', $this->formatter->format(1.23));
    }

    #[Test]
    public function it_formats_strings(): void
    {
        $this->assertSame('string', $this->formatter->format('STRING'));
    }

    #[Test]
    public function it_formats_arrays(): void
    {
        $this->assertSame('array', $this->formatter->format([1, 2, 3]));
    }

    #[Test]
    public function it_formats_closures(): void
    {
        $closure = function () {
            return 42;
        };

        $this->assertSame('closure', $this->formatter->format($closure));
    }

    #[Test]
    public function it_formats_enums(): void
    {
        $enum = SimpleEnum::ONE;

        $this->assertSame('enum(Tests\Fixtures\SimpleEnum)', $this->formatter->format($enum));
    }

    #[Test]
    public function it_formats_backed_enums(): void
    {
        $enum = SimpleBackedEnum::ONE;

        $this->assertSame('enum(Tests\Fixtures\SimpleBackedEnum)', $this->formatter->format($enum));
    }

    #[Test]
    public function it_formats_objects(): void
    {
        $obj = new class {
        };

        $this->assertSame(sprintf('object(%s)', get_class($obj)), $this->formatter->format($obj));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = new TypeFormatter;
    }
}
