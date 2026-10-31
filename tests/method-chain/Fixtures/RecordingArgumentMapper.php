<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

use Pathway\Info\MethodInfo;
use Pathway\Invocation\ArgumentMapper;

use function array_is_list;
use function is_array;

/**
 * Records each call. A list is returned untouched; anything else is wrapped in a list, so it is passed as the
 * method's only argument.
 */
final class RecordingArgumentMapper implements ArgumentMapper
{
    /**
     * @var list<array{method: string, value: mixed}>
     */
    public array $calls = [];

    public function resolve(MethodInfo $methodInfo, mixed $value): array
    {
        $this->calls[] = ['method' => $methodInfo->getName(), 'value' => $value];

        return is_array($value) && array_is_list($value) ? $value : [$value];
    }
}
