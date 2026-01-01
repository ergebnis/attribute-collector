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

final class ClassMethodCouldNotBeReflected extends \RuntimeException implements Exception
{
    public static function forClassMethodLocationWithReflectionException(
        Location\ClassMethodLocation $classMethodLocation,
        \ReflectionException $reflectionException,
    ): self {
        return new self(
            \sprintf(
                'Class method "%s::%s()" could not be reflected because a reflection exception was thrown with message "%s".',
                $classMethodLocation->className()->toString(),
                $classMethodLocation->methodName()->toString(),
                $reflectionException->getMessage(),
            ),
            0,
            $reflectionException,
        );
    }
}
