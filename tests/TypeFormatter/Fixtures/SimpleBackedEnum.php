<?php
declare(strict_types=1);

namespace Tests\TypeFormatter\Fixtures;

enum SimpleBackedEnum: string
{
    case ONE = 'TWO';
    case THREE = 'FOUR';
}
