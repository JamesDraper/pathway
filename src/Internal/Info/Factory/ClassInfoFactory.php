<?php
declare(strict_types=1);

namespace Pathway\Internal\Info\Factory;

use Pathway\Internal\Info\Factory\MethodInfoFactory;
use Pathway\Internal\Info\ClassInfo;

class ClassInfoFactory
{
    public function __construct(private readonly MethodInfoFactory $methodInfoFactory)
    {
    }

    public function make(string $class): MethodInfo
    {
        return new ClassInfo($this->methodInfoFactory, $class);
    }
}
