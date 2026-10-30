<?php

declare(strict_types=1);

namespace Pathway\Invocation;

use Pathway\Info\MethodInfo;

use function array_key_exists;
use function array_diff_key;
use function array_is_list;
use function array_flip;
use function array_keys;
use function array_push;
use function array_map;
use function is_array;

/**
 * @internal
 */
final class DefaultArgumentMapper implements ArgumentMapper
{
    public function resolve(MethodInfo $methodInfo, mixed $value): array
    {
        if (!is_array($value)) {
            throw DefaultArgumentMapperException::notAnArray(
                $methodInfo->getClassInfo()->getName(),
                $methodInfo->getName(),
                $value,
            );
        }

        if (array_is_list($value)) {
            return $value;
        }

        return $this->mapByName($methodInfo, $value);
    }

    /**
     * @param array<array-key, mixed> $value
     * @return list<mixed>
     * @throws DefaultArgumentMapperException
     */
    private function mapByName(MethodInfo $methodInfo, array $value): array
    {
        $class = $methodInfo->getClassInfo()->getName();
        $method = $methodInfo->getName();
        $parameterInfos = $methodInfo->getParameterInfos();

        $names = array_map(static fn($parameterInfo): string => $parameterInfo->getName(), $parameterInfos);

        $unknown = array_diff_key($value, array_flip($names));

        if ($unknown !== []) {
            throw DefaultArgumentMapperException::unknownParameters($class, $method, array_keys($unknown));
        }

        $arguments = [];

        // Defaults of skipped optional parameters, only needed if a later parameter is supplied.
        $pendingDefaults = [];

        foreach ($parameterInfos as $parameterInfo) {
            $name = $parameterInfo->getName();

            if ($parameterInfo->isVariadic()) {
                if (array_key_exists($name, $value)) {
                    $variadic = $value[$name];

                    if (!is_array($variadic) || !array_is_list($variadic)) {
                        throw DefaultArgumentMapperException::variadicNotAList($class, $method, $name, $variadic);
                    }

                    // Defaults are only needed to reach the variadic values, so an empty list adds nothing.
                    if ($variadic !== []) {
                        array_push($arguments, ...$pendingDefaults, ...$variadic);
                    }
                }

                break;
            }

            if (array_key_exists($name, $value)) {
                array_push($arguments, ...$pendingDefaults);
                $pendingDefaults = [];

                $arguments[] = $value[$name];

                continue;
            }

            if (!$parameterInfo->hasDefault()) {
                throw DefaultArgumentMapperException::missingArgument($class, $method, $name);
            }

            $pendingDefaults[] = $parameterInfo->getDefault();
        }

        return $arguments;
    }
}
