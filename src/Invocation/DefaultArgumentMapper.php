<?php

declare(strict_types=1);

namespace Pathway\Invocation;

use Pathway\Info\MethodInfo;
use Pathway\TypeFormatter;

use function array_key_exists;
use function array_diff_key;
use function array_is_list;
use function array_flip;
use function array_keys;
use function array_push;
use function array_map;
use function is_array;
use function implode;
use function sprintf;
use function count;

/**
 * @internal
 */
final class DefaultArgumentMapper implements ArgumentMapper
{
    public function resolve(MethodInfo $methodInfo, mixed $value): array
    {
        if (!is_array($value)) {
            throw $this->notAnArray($methodInfo, $value);
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
        $parameterInfos = $methodInfo->getParameterInfos();

        $names = array_map(static fn($parameterInfo): string => $parameterInfo->getName(), $parameterInfos);

        $unknown = array_diff_key($value, array_flip($names));

        if ($unknown !== []) {
            throw $this->unknownParameters($methodInfo, array_keys($unknown));
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
                        throw $this->variadicNotAList($methodInfo, $name, $variadic);
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
                throw $this->missingArgument($methodInfo, $name);
            }

            $pendingDefaults[] = $parameterInfo->getDefault();
        }

        return $arguments;
    }

    private function describe(MethodInfo $methodInfo): string
    {
        return sprintf('%s::%s', $methodInfo->getClassInfo()->getName(), $methodInfo->getName());
    }

    private function notAnArray(MethodInfo $methodInfo, mixed $value): DefaultArgumentMapperException
    {
        return new DefaultArgumentMapperException(sprintf(
            'Arguments for method "%s" must be an array, "%s" given.',
            $this->describe($methodInfo),
            TypeFormatter::format($value),
        ));
    }

    /**
     * @param list<array-key> $keys
     */
    private function unknownParameters(MethodInfo $methodInfo, array $keys): DefaultArgumentMapperException
    {
        $quoted = implode(', ', array_map(static fn($key): string => sprintf('"%s"', $key), $keys));

        return new DefaultArgumentMapperException(sprintf(
            'Method "%s" does not have %s named %s.',
            $this->describe($methodInfo),
            count($keys) === 1 ? 'a parameter' : 'parameters',
            $quoted,
        ));
    }

    private function missingArgument(MethodInfo $methodInfo, string $name): DefaultArgumentMapperException
    {
        return new DefaultArgumentMapperException(sprintf(
            'Missing required argument "%s" for method "%s".',
            $name,
            $this->describe($methodInfo),
        ));
    }

    private function variadicNotAList(MethodInfo $methodInfo, string $name, mixed $value): DefaultArgumentMapperException
    {
        return new DefaultArgumentMapperException(sprintf(
            'Argument "%s" for variadic parameter of method "%s" must be a list, "%s" given.',
            $name,
            $this->describe($methodInfo),
            TypeFormatter::format($value),
        ));
    }
}
