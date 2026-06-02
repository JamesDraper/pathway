<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

use Pathway\Internal\Info\Factory\MethodInfoFactory;

use function array_key_exists;
use function method_exists;

/**
 * @internal
 */
class ClassInfo
{
    /**
     * @var array<string, MethodInfo|null> $methodInfos
     */
    private array $methodInfos = [];

    /**
     * @param class-string $class
     */
    public function __construct(
        private readonly MethodInfoFactory $methodInfoFactory,
        private readonly string $class
    ) {
    }

    public function getName(): string
    {
        return $this->class;
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
        if (!method_exists($this->class, $method)) {
            return null;
        }

        return $this->methodInfoFactory->make($this->class, $method);
    }
}
