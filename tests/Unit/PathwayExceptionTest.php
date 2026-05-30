<?php
declare(strict_types=1);

namespace Tests\Unit;

use Pathway\PathwayException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class PathwayExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(PathwayException::class);
    }
}
