<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Type;

use Countable;
use Iterator;

use function var_export;
use function count;

final class IntersectionParameter
{
    public function printCollection(Countable&Iterator $collection): void
    {
        echo count($collection) . ' items in collection.' . "\n";
        echo "\n";

        foreach ($collection as $i => $item) {
            echo 'Item #' . $i . ' = ' . var_export($item, true) . "\n";
            echo "\n";
        }
    }
}
