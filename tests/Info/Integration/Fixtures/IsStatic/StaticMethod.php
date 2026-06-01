<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\IsStatic;

use function strrev;

final class StaticMethod
{
    public static function reverse(string $str): string
    {
        return strrev($str);
    }
}
