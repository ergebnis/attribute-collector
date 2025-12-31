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

final class AttributeCollectionNotSupported extends \InvalidArgumentException implements Exception
{
    public static function forLocation(Location\Location $location): self
    {
        return new self(\sprintf(
            'Attribute collection is not supported for locations of type "%s".',
            $location::class,
        ));
    }
}
