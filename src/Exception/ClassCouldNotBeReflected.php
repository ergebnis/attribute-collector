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

final class ClassCouldNotBeReflected extends \RuntimeException implements Exception
{
    public static function forClassLocationWithReflectionException(
        Location\ClassLocation $classLocation,
        \ReflectionException $reflectionException,
    ): self {
        return new self(
            \sprintf(
                'Class "%s" could not be reflected because a reflection exception was thrown with message "%s".',
                $classLocation->className()->toString(),
                $reflectionException->getMessage(),
            ),
            0,
            $reflectionException,
        );
    }
}
