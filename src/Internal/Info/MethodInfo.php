<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

use ReflectionMethod;

/**
 * @internal
 */
final class MethodInfo
{
    /**
     * @param list<ParameterInfo> $parameterInfos
     */
    public function __construct(
        private readonly ReflectionMethod $method,
        private readonly array $parameterInfos
    ) {
    }

    public function getVisibility(): Visibility
    {
        return match (true) {
            $this->method->isPublic() => Visibility::PUBLIC,
            $this->method->isProtected() => Visibility::PROTECTED,
            default => Visibility::PRIVATE,
        };
    }

    public function isStatic(): bool
    {
        return $this->method->isStatic();
    }

    public function getName(): string
    {
        return $this->method->getName();
    }

    /**
     * @return list<ParameterInfo>
     */
    public function getParameterInfos(): array
    {
        return $this->parameterInfos;
    }
}
