<?php
declare(strict_types=1);

namespace Pathway\Internal\Info\Factory;

use Pathway\Internal\Info\Factory\MethodInfoFactory;
use Pathway\Internal\Info\ClassInfo;

use function class_exists;

class ClassInfoFactory
{
    public function __construct(private readonly MethodInfoFactory $methodInfoFactory)
    {
    }

    public function make(string $class): ?ClassInfo
    {
        if (!class_exists($class)) {
            return null;
        }

        return new ClassInfo($this->methodInfoFactory, $class);
    }
}
