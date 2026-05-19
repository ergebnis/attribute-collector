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

final class AttributeInstanceCouldNotBeExported extends \RuntimeException implements Exception
{
    public static function forAttributeInstanceWithUnsupportedParameterValueType(
        object $instance,
        string $parameterName,
        string $valueType,
    ): self {
        return new self(\sprintf(
            'Attribute instance of class "%s" could not be exported because the value of constructor parameter "$%s" has the unsupported type "%s".',
            $instance::class,
            $parameterName,
            $valueType,
        ));
    }
}
