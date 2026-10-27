<?php

declare(strict_types=1);

namespace Pathway\Invocation;

use Pathway\Info\MethodInfo;

interface ArgumentMapper
{
    /**
     * @return list<mixed>
     * @throws ArgumentMapperException
     */
    public function resolve(MethodInfo $methodInfo, mixed $value): array;
}
