<?php
declare(strict_types=1);

namespace Tests\Info\Unit\ClassInfo;

use Pathway\Internal\Info\Factory\MethodInfoFactory;
use Pathway\Internal\Info\MethodInfo;
use Pathway\Internal\Info\ClassInfo;

use Tests\Info\Unit\ClassInfoTest\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;

final class ClassInfoTest extends TestCase
{
    private MockInterface&MethodInfoFactory $methodInfoFactory;

    private ClassInfo $classInfo;

    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(ClassInfo::class);
    }

    #[Test]
    public function it_gets_method_info_if_method_exists(): void
    {
        $methodInfo = $this->makeMock(MethodInfo::class);

        $this
            ->methodInfoFactory
            ->expects()
            ->make(Fixtures\SimpleClass::class, 'print')
            ->andReturn($methodInfo);

        $result = $this->classInfo->getMethodInfo('print');

        $this->assertSame($methodInfo, $result);
    }

    #[Test]
    public function it_only_makes_a_method_info_once_per_method(): void
    {
        $methodInfo = $this->makeMock(MethodInfo::class);

        $this
            ->methodInfoFactory
            ->expects()
            ->make(Fixtures\SimpleClass::class, 'print')
            ->andReturn($methodInfo);

        $result1 = $this->classInfo->getMethodInfo('print');
        $result2 = $this->classInfo->getMethodInfo('print');

        $this->assertSame($result1, $result2);
    }

    #[Test]
    public function it_returns_null_if_method_does_not_exist(): void
    {
        $methodInfo = $this->makeMock(MethodInfo::class);

        $result = $this->classInfo->getMethodInfo('_not_a_method');

        $this->assertNull($result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->methodInfoFactory = $this->makeMock(MethodInfoFactory::class);

        $this->classInfo = new ClassInfo($this->methodInfoFactory, Fixtures\SimpleClass::class);
    }
}
