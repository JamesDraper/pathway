<?php
declare(strict_types=1);

namespace Tests\Info\Unit\ParameterInfoTest;

use Pathway\Internal\Info\ParameterInfo;
use Pathway\Internal\Info\TypeInfo;

use Tests\Info\Unit\ParameterInfoTest\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionParameter;
use ReflectionClass;

use function array_shift;

final class ParameterInfoTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(ParameterInfo::class);
    }

    #[Test]
    #[DataProvider('provider_it_gets_parameter_info')]
    public function it_gets_parameter_info(
        string $expectedName,
        bool $expectedIsVariadric,
        bool $expectedHasDefault,
        mixed $expectedDefault,
        string $class,
        string $method,
    ): void {
        $reflectionParameter = $this->getReflectionParameter($class, $method);

        $typeInfo = $this->makeMock(TypeInfo::class);

        $parameterInfo = new ParameterInfo($reflectionParameter, $typeInfo);

        $this->assertSame($expectedName, $parameterInfo->getName());
        $this->assertSame($expectedIsVariadric, $parameterInfo->isVariadic());
        $this->assertSame($expectedHasDefault, $parameterInfo->hasDefault());
        $this->assertSame($expectedDefault, $parameterInfo->getDefault());
        $this->assertSame($typeInfo, $parameterInfo->getTypeInfo());
    }

    public static function provider_it_gets_parameter_info(): array
    {
        return [
            [
                'expectedName' => 'str',
                'expectedIsVariadric' => false,
                'expectedHasDefault' => false,
                'expectedDefault' => null,
                'class' => Fixtures\SimpleParameter::class,
                'method' => 'reverse',
            ],
            [
                'expectedName' => 'strs',
                'expectedIsVariadric' => true,
                'expectedHasDefault' => false,
                'expectedDefault' => null,
                'class' => Fixtures\VariadicParameter::class,
                'method' => 'concatenate',
            ],
            [
                'expectedName' => 'user',
                'expectedIsVariadric' => false,
                'expectedHasDefault' => true,
                'expectedDefault' => 'guest',
                'class' => Fixtures\DefaultValueParameter::class,
                'method' => 'greet',
            ],
        ];
    }

    private function getReflectionParameter(string $class, string $method): ReflectionParameter
    {
        $reflectionClass = new ReflectionClass($class);

        $reflectionParameters = $reflectionClass->getMethod($method)->getParameters();

        return array_shift($reflectionParameters);
    }
}
