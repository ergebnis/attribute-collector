<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/attribute-collector
 */

namespace Ergebnis\AttributeCollector\Exception;

use Ergebnis\AttributeCollector\Location;

final class FunctionCouldNotBeReflected extends \RuntimeException implements Exception
{
    public static function forFunctionLocationWithReflectionException(
        Location\FunctionLocation $functionLocation,
        \ReflectionException $reflectionException,
    ): self {
        return new self(
            \sprintf(
                'Function "%s" could not be reflected because a reflection exception was thrown with message "%s".',
                $functionLocation->functionName()->toString(),
                $reflectionException->getMessage(),
            ),
            0,
            $reflectionException,
        );
    }
}
