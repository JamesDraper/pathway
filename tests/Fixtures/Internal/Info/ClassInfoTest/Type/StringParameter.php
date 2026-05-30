<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Type;

use function str_split;

final class StringParameter
{
    /**
     * @return list<string>
     */
    public function split(string $str): array
    {
        return str_split($str);
    }
}
