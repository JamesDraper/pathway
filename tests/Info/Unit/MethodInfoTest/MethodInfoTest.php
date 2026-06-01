<?php
declare(strict_types=1);

namespace Tests\Info\Unit\MethodInfoTest;

use Pathway\Internal\Info\MethodInfo;
use Pathway\Internal\Info\Visibility;

use Tests\Info\Unit\MethodInfoTest\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionMethod;

final class MethodInfoTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(MethodInfo::class);
    }

    #[Test]
    #[DataProvider('provider_it_gets_method_info')]
    public function it_gets_method_info(
        Visibility $expectedVisibility,
        bool $expectedIsStatic,
        string $class,
        string $method,
    ): void {
        $parameterInfos = [
            $this->makeMock(ParameterInfo::class),
            $this->makeMock(ParameterInfo::class),
        ];

        $methodInfo = new MethodInfo(
            new ReflectionMethod($class, $method),
            $parameterInfos,
        );

        $this->assertSame($expectedVisibility, $methodInfo->getVisibility());
        $this->assertSame($expectedIsStatic, $methodInfo->isStatic());
        $this->assertSame($method, $methodInfo->getName());
        $this->assertSame($parameterInfos, $methodInfo->getParameterInfos());
    }

    public static function provider_it_gets_method_info(): array
    {
        return [
            'public method' => [
                'expectedVisibility' => Visibility::PUBLIC,
                'expectedIsStatic' => false,
                'class' => Fixtures\PublicMethod::class,
                'method' => 'greetPublic',
            ],
            'protected method' => [
                'expectedVisibility' => Visibility::PROTECTED,
                'expectedIsStatic' => false,
                'class' => Fixtures\ProtectedMethod::class,
                'method' => 'greetProtected',
            ],
            'private method' => [
                'expectedVisibility' => Visibility::PRIVATE,
                'expectedIsStatic' => false,
                'class' => Fixtures\PrivateMethod::class,
                'method' => 'greetPrivate',
            ],
            'no visibility method' => [
                'expectedVisibility' => Visibility::PUBLIC,
                'expectedIsStatic' => false,
                'class' => Fixtures\NoVisibilityMethod::class,
                'method' => 'greetNoVisibility',
            ],
            'static' => [
                'expectedVisibility' => Visibility::PUBLIC,
                'expectedIsStatic' => true,
                'class' => Fixtures\StaticMethod::class,
                'method' => 'greetPublic',
            ],
        ];
    }
}
