<?php

declare(strict_types=1);

namespace Tests\Registry;

use Pathway\Internal\Info\Factories\ClassInfoFactory;
use Pathway\Internal\Info\ClassInfo;
use Pathway\Internal\DefaultHandlerLocator;
use Pathway\Internal\TypeFormatter;
use Pathway\Internal\Registry;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionProperty;
use ReflectionMethod;

final class RegistryTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(Registry::class);
    }

    #[Test]
    public function it_cannot_be_instantiated(): void
    {
        $constructor = new ReflectionMethod(Registry::class, '__construct');

        $this->assertTrue($constructor->isPrivate());
    }

    #[Test]
    #[DataProvider('provider_classes')]
    public function it_returns_an_instance_of_the_class(string $expected, string $method, string $property): void
    {
        $this->assertInstanceOf($expected, Registry::$method());
    }

    #[Test]
    #[DataProvider('provider_classes')]
    public function it_returns_the_same_instance_on_subsequent_calls(string $expected, string $method, string $property): void
    {
        $first = Registry::$method();
        $second = Registry::$method();

        $this->assertSame($first, $second);
    }

    #[Test]
    #[DataProvider('provider_classes')]
    public function it_caches_the_instance_to_a_private_static_property(string $expected, string $method, string $property): void
    {
        $reflectionProperty = new ReflectionProperty(Registry::class, $property);

        $this->assertTrue($reflectionProperty->isPrivate());
        $this->assertTrue($reflectionProperty->isStatic());
        $this->assertNull($reflectionProperty->getValue());

        $instance = Registry::$method();

        $this->assertSame($instance, $reflectionProperty->getValue());
    }

    #[Test]
    #[DataProvider('provider_classes')]
    public function it_returns_the_cached_instance_if_one_exists(string $expected, string $method, string $property): void
    {
        $reflectionProperty = new ReflectionProperty(Registry::class, $property);
        $cached = Registry::$method();

        // Replace the cached instance to prove the registry returns what is cached rather than creating a new one.
        $replacement = clone $cached;
        $reflectionProperty->setValue(null, $replacement);

        $this->assertSame($replacement, Registry::$method());
    }

    #[Test]
    public function its_class_info_factory_is_fully_wired(): void
    {
        $classInfo = Registry::getClassInfoFactory()->make(Registry::class);

        $this->assertInstanceOf(ClassInfo::class, $classInfo);
        $this->assertNotNull($classInfo->getMethodInfo('getClassInfoFactory'));
    }

    #[Test]
    public function its_default_handler_locator_shares_the_registry_dependencies(): void
    {
        $locator = Registry::getDefaultHandlerLocator();

        $classInfoFactory = new ReflectionProperty(DefaultHandlerLocator::class, 'classInfoFactory');
        $typeFormatter = new ReflectionProperty(DefaultHandlerLocator::class, 'typeFormatter');

        $this->assertSame(Registry::getClassInfoFactory(), $classInfoFactory->getValue($locator));
        $this->assertSame(Registry::getTypeFormatter(), $typeFormatter->getValue($locator));
    }

    public static function provider_classes(): array
    {
        return [
            'type formatter' => [
                'expected' => TypeFormatter::class,
                'method' => 'getTypeFormatter',
                'property' => 'typeFormatter',
            ],
            'class info factory' => [
                'expected' => ClassInfoFactory::class,
                'method' => 'getClassInfoFactory',
                'property' => 'classInfoFactory',
            ],
            'default handler locator' => [
                'expected' => DefaultHandlerLocator::class,
                'method' => 'getDefaultHandlerLocator',
                'property' => 'defaultHandlerLocator',
            ],
        ];
    }
}
