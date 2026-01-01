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

final class ClassMethodDoesNotHaveParameter extends \RuntimeException implements Exception
{
    public static function forClassMethodParameterLocation(Location\ClassMethodParameterLocation $classMethodParameterLocation): self
    {
        return new self(\sprintf(
            'Class method "%s::%s()" does not have a parameter "$%s".',
            $classMethodParameterLocation->className()->toString(),
            $classMethodParameterLocation->methodName()->toString(),
            $classMethodParameterLocation->parameterName()->toString(),
        ));
    }
}
