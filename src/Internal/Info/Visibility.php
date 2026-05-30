<?php
declare(strict_types=1);

namespace Pathway\Internal\Info;

/**
 * @internal
 */
enum Visibility
{
    case PUBLIC;
    case PROTECTED;
    case PRIVATE;
}
