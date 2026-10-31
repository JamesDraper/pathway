<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

use Pathway\Invocation\ArgumentMapper;
use Pathway\Info\MethodInfo;

/**
 * Records each call and returns the value untouched, so the tests can pass the arguments directly.
 */
final class RecordingArgumentMapper implements ArgumentMapper
{
    /**
     * @var list<array{methodInfo: MethodInfo, value: mixed}>
     */
    public array $calls = [];

    public function resolve(MethodInfo $methodInfo, mixed $value): array
    {
        $this->calls[] = ['methodInfo' => $methodInfo, 'value' => $value];

        /** @var list<mixed> $value */
        return $value;
    }
}
