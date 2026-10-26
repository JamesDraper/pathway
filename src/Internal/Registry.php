<?php

declare(strict_types=1);

namespace Pathway\Internal;

use Pathway\Internal\Info\Factories\ParameterInfoFactory;
use Pathway\Internal\Info\Factories\MethodInfoFactory;
use Pathway\Internal\Info\Factories\ClassInfoFactory;
use Pathway\Internal\Info\Factories\TypeInfoFactory;

/**
 * @internal
 */
class Registry
{
    private static ?TypeFormatter $typeFormatter = null;

    private static ?ClassInfoFactory $classInfoFactory = null;

    private static ?DefaultHandlerLocator $defaultHandlerLocator = null;

    public static function getDefaultHandlerLocator(): DefaultHandlerLocator
    {
        return self::$defaultHandlerLocator ??= new DefaultHandlerLocator(
            self::getClassInfoFactory(),
            self::getTypeFormatter(),
        );
    }

    public static function getTypeFormatter(): TypeFormatter
    {
        return self::$typeFormatter ??= new TypeFormatter();
    }

    public static function getClassInfoFactory(): ClassInfoFactory
    {
        return self::$classInfoFactory ??= new ClassInfoFactory(
            new MethodInfoFactory(
                new ParameterInfoFactory(
                    new TypeInfoFactory(),
                ),
            ),
        );
    }

    private function __construct() {}
}
