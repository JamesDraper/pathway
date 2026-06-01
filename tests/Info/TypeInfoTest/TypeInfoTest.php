<?php
declare(strict_types=1);

namespace Tests\Info\TypeInfoTest;

use Pathway\Internal\Info\TypeInfo;

use Tests\Info\TypeInfoTest\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionFunction;
use ReflectionType;
use Closure;

use function array_values;
use function array_merge;
use function array_map;
use function vsprintf;

final class TypeInfoTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(TypeInfo::class);
    }

    #[Test]
    #[DataProvider('providerIsCompatible')]
    public function isCompatible_checks_that_a_type_is_compatible_with_a_value(
        bool $expected,
        string $type,
        mixed $value,
    ): void {
        $reflectionType = $this->getReflectionType($type);
        $typeInfo = new TypeInfo($reflectionType);

        $result = $typeInfo->isCompatible($value);

        $this->assertSame($expected, $result);
    }

    public static function providerIsCompatible(): array
    {
        return array_merge(
            self::isCompatibleScenariosSimpleType(),
            self::isCompatibleScenariosNullable(),
            self::isCompatibleScenariosComplexType(),
            self::isCompatibleScenariosUnionType(),
            self::isCompatibleScenariosIntersectionType(),
        );
    }

    #[Test]
    #[DataProvider('providerIsCompatibleNoType')]
    public function isCompatible_always_returns_true_if_type_is_null(mixed $value): void
    {
        $typeInfo = new TypeInfo(null);

        $result = $typeInfo->isCompatible($value);

        $this->assertTrue($result);
    }

    public static function providerIsCompatibleNoType(): array
    {
        $scenarios = self::providerIsCompatible();

        $scenarios = array_map(function (array $scenario): array {
            unset($scenario['expected']);
            unset($scenario['type']);

            return $scenario;
        }, $scenarios);

        return $scenarios;
    }

    #[Test]
    #[DataProvider('providerToString')]
    public function it_converts_types_to_strings(string $expected, string $type): void
    {
        $reflectionType = $this->getReflectionType($type);
        $typeInfo = new TypeInfo($reflectionType);

        $this->assertSame($expected, $typeInfo->toString());
    }

    public static function providerToString(): array
    {
        return [
            [
                'expected' => 'string',
                'type' => 'string',
            ],
            [
                'expected' => 'string|null',
                'type' => '?string',
            ],
            [
                'expected' => 'string|null',
                'type' => 'string|null',
            ],
            [
                'expected' => 'int',
                'type' => 'int',
            ],
            [
                'expected' => 'int|null',
                'type' => '?int',
            ],
            [
                'expected' => 'int|null',
                'type' => 'int|null',
            ],
            [
                'expected' => 'float',
                'type' => 'float',
            ],
            [
                'expected' => 'float|null',
                'type' => '?float',
            ],
            [
                'expected' => 'float|null',
                'type' => 'float|null',
            ],
            [
                'expected' => 'bool',
                'type' => 'bool',
            ],
            [
                'expected' => 'bool|null',
                'type' => '?bool',
            ],
            [
                'expected' => 'bool|null',
                'type' => 'bool|null',
            ],
            [
                'expected' => 'bool|int',
                'type' => 'bool|int',
            ],
            [
                'expected' => 'bool|int|null',
                'type' => 'bool|int|null',
            ],
            [
                'expected' => Fixtures\Classes\ClassA::class,
                'type' => Fixtures\Classes\ClassA::class,
            ],
            [
                'expected' => Fixtures\Classes\ClassB::class,
                'type' => Fixtures\Classes\ClassB::class,
            ],
            [
                'expected' => Fixtures\Classes\ClassC::class,
                'type' => Fixtures\Classes\ClassC::class,
            ],
            [
                'expected' => Fixtures\Classes\ClassD::class,
                'type' => Fixtures\Classes\ClassD::class,
            ],
            [
                'expected' => Fixtures\Classes\ClassE::class,
                'type' => Fixtures\Classes\ClassE::class,
            ],
            [
                'expected' => Fixtures\Classes\ClassA::class . '|' . Fixtures\Classes\ClassB::class,
                'type' => Fixtures\Classes\ClassA::class . '|' . Fixtures\Classes\ClassB::class,
            ],
            [
                'expected' => Fixtures\Classes\ClassA::class . '&' . Fixtures\Classes\ClassB::class,
                'type' => Fixtures\Classes\ClassA::class . '&' . Fixtures\Classes\ClassB::class,
            ],
            [
                'expected' => vsprintf('(%s&%s)|%s', [
                    Fixtures\Classes\ClassA::class,
                    Fixtures\Classes\ClassB::class,
                    Fixtures\Classes\ClassC::class,
                ]),
                'type' => vsprintf('(%s&%s)|%s', [
                    Fixtures\Classes\ClassA::class,
                    Fixtures\Classes\ClassB::class,
                    Fixtures\Classes\ClassC::class,
                ]),
            ],
            [
                'expected' => vsprintf('(%s&%s)|(%s&%s)', [
                    Fixtures\Classes\ClassA::class,
                    Fixtures\Classes\ClassB::class,
                    Fixtures\Classes\ClassC::class,
                    Fixtures\Classes\ClassD::class,
                ]),
                'type' => vsprintf('(%s&%s)|(%s&%s)', [
                    Fixtures\Classes\ClassA::class,
                    Fixtures\Classes\ClassB::class,
                    Fixtures\Classes\ClassC::class,
                    Fixtures\Classes\ClassD::class,
                ]),
            ],
        ];
    }

    #[Test]
    public function toString_returns_an_empty_string_with_null_type(): void
    {
        $typeInfo = new TypeInfo(null);

        $this->assertSame('', $typeInfo->toString());
    }

    private static function isCompatibleScenariosSimpleType(): array
    {
        $stringIsCompatibleWithString = [
            [
                'expected' => true,
                'type' => 'string',
                'value' => '',
            ],
        ];

        $intIsNotCompatibleWithString = [
            [
                'expected' => false,
                'type' => 'string',
                'value' => 123,
            ],
        ];

        $intIsCompatibleWithFloat = [
            [
                'expected' => true,
                'type' => 'float',
                'value' => 123,
            ],
        ];

        $floatIsNotCompatibleWithInt = [
            [
                'expected' => false,
                'type' => 'int',
                'value' => 123.0,
            ],
        ];

        return array_merge(
            $stringIsCompatibleWithString,
            $intIsNotCompatibleWithString,
            $intIsCompatibleWithFloat,
            $floatIsNotCompatibleWithInt,
        );
    }

    private static function isCompatibleScenariosNullable(): array
    {
        return [
            [
                'expected' => true,
                'type' => '?string',
                'value' => '',
            ],
            [
                'expected' => true,
                'type' => '?string',
                'value' => null,
            ],
        ];
    }

    private static function isCompatibleScenariosComplexType(): array
    {
        $objectIsCompatibleWithClass = [
            [
                'expected' => true,
                'type' => Fixtures\Classes\ClassA::class,
                'value' => new Fixtures\Classes\ClassA(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Classes\ClassB::class,
                'value' => new Fixtures\Classes\ClassB(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Classes\ClassC::class,
                'value' => new Fixtures\Classes\ClassC(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Classes\ClassD::class,
                'value' => new Fixtures\Classes\ClassD(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Classes\ClassE::class,
                'value' => new Fixtures\Classes\ClassE(),
            ],
        ];

        $objectIsNotCompatibleWithClass = [
            [
                'expected' => false,
                'type' => Fixtures\Classes\ClassB::class,
                'value' => new Fixtures\Classes\ClassA(),
            ],
            [
                'expected' => false,
                'type' => Fixtures\Classes\ClassC::class,
                'value' => new Fixtures\Classes\ClassA(),
            ],
        ];

        $objectIsCompatibleWithInterface = [
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceA::class,
                'value' => new Fixtures\Classes\ClassA(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceB::class,
                'value' => new Fixtures\Classes\ClassB(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceC::class,
                'value' => new Fixtures\Classes\ClassC(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassD(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceE::class,
                'value' => new Fixtures\Classes\ClassE(),
            ],
        ];

        $objectIsNotCompatibleWithInterface = [
            [
                'expected' => false,
                'type' => Fixtures\Interfaces\InterfaceC::class,
                'value' => new Fixtures\Classes\ClassA(),
            ],
            [
                'expected' => false,
                'type' => Fixtures\Interfaces\InterfaceC::class,
                'value' => new Fixtures\Classes\ClassB(),
            ],
        ];

        return array_merge(
            $objectIsCompatibleWithClass,
            $objectIsNotCompatibleWithClass,
            $objectIsCompatibleWithInterface,
            $objectIsNotCompatibleWithInterface,
        );
    }

    private function getReflectionType(string $type): ReflectionType
    {
        $src = vsprintf('return function(%s $a) {};', [$type]);

        /**
         * @var Closure $function
         */
        $function = eval($src);

        $reflectionFunction = new ReflectionFunction($function);

        /**
         * @var ReflectionType $reflectionType
         */
        $reflectionType = $reflectionFunction->getParameters()[0]->getType();

        return $reflectionType;
    }

    private static function isCompatibleScenariosUnionType(): array
    {
        $nonObjectIsCompatibleWithType = [
            [
                'expected' => true,
                'type' => 'string|int',
                'value' => 123,
            ],
            [
                'expected' => true,
                'type' => 'string|int',
                'value' => '',
            ],
        ];

        $nonObjectIsNotCompatibleWithType = [
            [
                'expected' => false,
                'type' => 'string|int',
                'value' => false,
            ],
        ];

        $objectIsCompatibleWithInterface = [
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceC::class . '|' . Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassC(),
            ],
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceC::class . '|' . Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassD(),
            ],
        ];

        $objectIsNotCompatibleWithInterface = [
            [
                'expected' => false,
                'type' => Fixtures\Interfaces\InterfaceC::class . '|' . Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassA(),
            ],
        ];

        return array_merge(
            $nonObjectIsCompatibleWithType,
            $nonObjectIsNotCompatibleWithType,
            $objectIsCompatibleWithInterface,
            $objectIsNotCompatibleWithInterface,
        );
    }

    private static function isCompatibleScenariosIntersectionType(): array
    {
        $objectIsCompatibleWithInterface = [
            [
                'expected' => true,
                'type' => Fixtures\Interfaces\InterfaceC::class . '&' . Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassE(),
            ],
        ];

        $objectIsNotCompatibleWithInterface = [
            [
                'expected' => false,
                'type' => Fixtures\Interfaces\InterfaceC::class . '&' . Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassA(),
            ],
            [
                'expected' => false,
                'type' => Fixtures\Interfaces\InterfaceC::class . '&' . Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassC(),
            ],
            [
                'expected' => false,
                'type' => Fixtures\Interfaces\InterfaceC::class . '&' . Fixtures\Interfaces\InterfaceD::class,
                'value' => new Fixtures\Classes\ClassD(),
            ],
        ];

        return array_merge(
            $objectIsCompatibleWithInterface,
            $objectIsNotCompatibleWithInterface,
        );
    }
}
