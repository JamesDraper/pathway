<?php
declare(strict_types=1);

namespace Tests\Unit\Internal\Info;

use Pathway\Internal\Info\ParameterInfo;
use Pathway\Internal\Info\TypeInfo;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionParameter;
use ReflectionFunction;
use Closure;

use function sprintf;

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
        string $parameterString,
    ): void {
        $reflectionParameter = $this->getReflectionParameter($parameterString);

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
                'expectedName' => 'one',
                'expectedIsVariadric' => false,
                'expectedHasDefault' => false,
                'expectedDefault' => null,
                'parameterString' => 'string $one',
            ],
            [
                'expectedName' => 'two',
                'expectedIsVariadric' => true,
                'expectedHasDefault' => false,
                'expectedDefault' => null,
                'parameterString' => 'string ...$two',
            ],
            [
                'expectedName' => 'three',
                'expectedIsVariadric' => false,
                'expectedHasDefault' => true,
                'expectedDefault' => 'THREE',
                'parameterString' => 'string $three = "THREE"',
            ],
        ];
    }

    private function getReflectionParameter(string $type): ReflectionParameter
    {
        $src = sprintf('return function(%s) {};', $type);

        /**
         * @var Closure $function
         */
        $function = eval($src);

        $reflectionFunction = new ReflectionFunction($function);

        return $reflectionFunction->getParameters()[0];
    }
}
