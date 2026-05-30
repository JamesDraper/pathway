<?php
declare(strict_types=1);

namespace Tests\Integration\Internal\Info;

use Pathway\Internal\Info\ParameterInfo;
use Pathway\Internal\Info\MethodInfo;
use Pathway\Internal\Info\Visibility;
use Pathway\Internal\Info\ClassInfo;

use Tests\Fixtures\Internal\Info\ClassInfoTest as Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionClass;

use function array_merge;
use function count;

final class ClassInfoTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(ClassInfo::class);
    }

    #[Test]
    public function getMethodInfo_returns_null_if_no_method_exists(): void
    {
        $classInfo = new ClassInfo(
            new ReflectionClass(
                Fixtures\Visibility\PublicMethod::class,
            ),
        );

        $this->assertNull($classInfo->getMethodInfo('_not_a_method'));
    }

    /**
     * @param class-string $class
     */
    #[Test]
    #[DataProvider('provider_getMethodInfo_returns_all_info')]
    public function getMethodInfo_returns_all_info(
        array $expected,
        string $class,
        string $method,
    ): void {
        $classInfo = new ClassInfo(new ReflectionClass($class));
        $methodInfo = $classInfo->getMethodInfo($method);

        $this->assertNotNull($methodInfo);

        $this->assertSame($expected['visibility'], $methodInfo->getVisibility());
        $this->assertSame($expected['isStatic'], $methodInfo->isStatic());
        $this->assertSame($method, $methodInfo->getName());

        $parameterInfos = $methodInfo->getParameterInfos();

        $this->assertCount(count($expected['parameters']), $parameterInfos);

        foreach ($expected['parameters'] as $i => $expectedParameter) {
            $parameterInfo = $parameterInfos[$i];

            $this->assertSame($expectedParameter['name'], $parameterInfo->getName());
            $this->assertSame($expectedParameter['isVariadic'], $parameterInfo->isVariadic());
            $this->assertSame($expectedParameter['hasDefault'], $parameterInfo->hasDefault());
            $this->assertSame($expectedParameter['default'], $parameterInfo->getDefault());

            $this->assertSame($expectedParameter['type'], $parameterInfo->getTypeInfo()->toString());
        }
    }

    public static function provider_getMethodInfo_returns_all_info(): array
    {
        $visibility = [
            'public method' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [],
                ],
                'class' => Fixtures\Visibility\PublicMethod::class,
                'method' => 'greetPublic',
            ],
            'protected method' => [
                'expected' => [
                    'visibility' => Visibility::PROTECTED,
                    'isStatic' => false,
                    'parameters' => [],
                ],
                'class' => Fixtures\Visibility\ProtectedMethod::class,
                'method' => 'greetProtected',
            ],
            'private method' => [
                'expected' => [
                    'visibility' => Visibility::PRIVATE,
                    'isStatic' => false,
                    'parameters' => [],
                ],
                'class' => Fixtures\Visibility\PrivateMethod::class,
                'method' => 'greetPrivate',
            ],
        ];

        $isStatic = [
            'static method' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => true,
                    'parameters' => [
                        [
                            'name' => 'str',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'string',
                        ],
                    ],
                ],
                'class' => Fixtures\IsStatic\StaticMethod::class,
                'method' => 'reverse',
            ],
            'non static method' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'str',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'string',
                        ],
                    ],
                ],
                'class' => Fixtures\IsStatic\NonStaticMethod::class,
                'method' => 'reverse',
            ],
        ];

        $variadic = [
            'variadic parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'ints',
                            'isVariadic' => true,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'int',
                        ],
                    ],
                ],
                'class' => Fixtures\Variadic\VariadicParameter::class,
                'method' => 'sum',
            ],
            'non variadic parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'ints',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'array',
                        ],
                    ],
                ],
                'class' => Fixtures\Variadic\NonVariadicParameter::class,
                'method' => 'sum',
            ],
        ];

        $type = [
            'int parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'i',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'int',
                        ],
                    ],
                ],
                'class' => Fixtures\Type\IntParameter::class,
                'method' => 'addThree',
            ],
            'string parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'str',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'string',
                        ],
                    ],
                ],
                'class' => Fixtures\Type\StringParameter::class,
                'method' => 'split',
            ],
            'float parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'f',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'float',
                        ],
                    ],
                ],
                'class' => Fixtures\Type\FloatParameter::class,
                'method' => 'addFour',
            ],
            'bool parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'b',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'bool',
                        ],
                    ],
                ],
                'class' => Fixtures\Type\BoolParameter::class,
                'method' => 'not',
            ],
            'class parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'dateTime',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'DateTime',
                        ],
                    ],
                ],
                'class' => Fixtures\Type\ClassParameter::class,
                'method' => 'format',
            ],
            'union parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'value',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'int|string',
                        ],
                    ],
                ],
                'class' => Fixtures\Type\UnionParameter::class,
                'method' => 'addFive',
            ],
            'intersection parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'collection',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'Countable&Iterator',
                        ],
                    ],
                ],
                'class' => Fixtures\Type\IntersectionParameter::class,
                'method' => 'printCollection',
            ],
        ];

        $nullable = [
            'nullable parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'id',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'string|null',
                        ],
                    ],
                ],
                'class' => Fixtures\Nullable\NullableParameter::class,
                'method' => 'setSessionId',
            ],
            'non nullable parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'id',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'string',
                        ],
                    ],
                ],
                'class' => Fixtures\Nullable\NonNullableParameter::class,
                'method' => 'setSessionId',
            ],
        ];

        $defaultValue = [
            'default value parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'user',
                            'isVariadic' => false,
                            'hasDefault' => true,
                            'default' => 'guest',
                            'type' => 'string',
                        ],
                    ],
                ],
                'class' => Fixtures\DefaultValue\DefaultValueParameter::class,
                'method' => 'greet',
            ],
            'non default value parameter' => [
                'expected' => [
                    'visibility' => Visibility::PUBLIC,
                    'isStatic' => false,
                    'parameters' => [
                        [
                            'name' => 'user',
                            'isVariadic' => false,
                            'hasDefault' => false,
                            'default' => null,
                            'type' => 'string',
                        ],
                    ],
                ],
                'class' => Fixtures\DefaultValue\NonDefaultValueParameter::class,
                'method' => 'greet',
            ],
        ];

        return array_merge(
            $visibility,
            $isStatic,
            $variadic,
            $type,
            $nullable,
        );
    }
}
