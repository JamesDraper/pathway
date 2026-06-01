<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

use Pathway\Internal\Info\Factory\MethodInfoFactory;

use ReflectionClass;

use function array_key_exists;

/**
 * @internal
 */
class ClassInfo
{
    /**
     * @var array<string, MethodInfo|null> $methodInfos
     */
    private array $methodInfos = [];

    // @phpstan-ignore missingType.generics
    public function __construct(
        private readonly MethodInfoFactory $methodInfoFactory,
        private readonly ReflectionClass $class
    ) {
    }
    
    public function getName(): string
    {
        return $this->class->getName();
    }

    public function getMethodInfo(string $method): ?MethodInfo
    {
        if (!array_key_exists($method, $this->methodInfos)) {
            $this->methodInfos[$method] = $this->makeMethodInfo($method);
        }

        return $this->methodInfos[$method];
    }

    private function makeMethodInfo(string $method): ?MethodInfo
    {
        if (!$this->class->hasMethod($methodName)) {
            return null;
        }

        return $this->makeMethodInfo->make(
            $this->class->getMethod($method),
        );
    }
}
