<?php

declare(strict_types=1);

namespace Tests\Info\Class;

use Pathway\Info\ClassInfo;

use Tests\Info\Class\Fixtures;
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
    public function it_passes_itself_to_the_method_info(): void
    {
        $result = $this->classInfo->getMethodInfo('print');

        $this->assertNotNull($result);
        $this->assertSame($this->classInfo, $result->getClassInfo());
    }

    #[Test]
    public function it_gets_the_class_name(): void
    {
        $this->assertSame(Fixtures\SimpleClass::class, $this->classInfo->getName());
    }

    #[Test]
    public function it_caches_a_missing_method_as_null(): void
    {
        $this->assertNull($this->classInfo->getMethodInfo('_not_a_method'));
        $this->assertNull($this->classInfo->getMethodInfo('_not_a_method'));
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
