<?php

declare(strict_types=1);

namespace Pathway\Internal;

use Pathway\Invocation\InvocationException;

use RuntimeException;

final class MethodChainException extends RuntimeException implements InvocationException {}
