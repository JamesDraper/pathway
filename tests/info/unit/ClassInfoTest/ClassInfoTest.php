<?php

declare(strict_types=1);

namespace Tests\Info\Unit\ClassInfo;

use Pathway\Internal\Info\ClassInfo;

use Tests\Info\Unit\ClassInfoTest\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class ClassInfoTest extends TestCase
{
    private ClassInfo $classInfo;

    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(ClassInfo::class);
    }

    #[Test]
    public function it_gets_method_info_if_method_exists(): void
    {
        $result = $this->classInfo->getMethodInfo('print');

        $this->assertNotNull($result);
        $this->assertSame('print', $result->getName());
    }

    #[Test]
    public function it_only_makes_a_method_info_once_per_method(): void
    {
        $result1 = $this->classInfo->getMethodInfo('print');
        $result2 = $this->classInfo->getMethodInfo('print');

        $this->assertNotNull($result1);
        $this->assertSame($result1, $result2);
    }

    #[Test]
    public function it_returns_null_if_method_does_not_exist(): void
    {
        $result = $this->classInfo->getMethodInfo('_not_a_method');

        $this->assertNull($result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->classInfo = new ClassInfo(Fixtures\SimpleClass::class);
    }
}
