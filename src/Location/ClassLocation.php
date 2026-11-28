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

namespace Ergebnis\AttributeCollector\Location;

use Ergebnis\AttributeCollector;

final class ClassLocation implements Location
{
    private function __construct(private AttributeCollector\Name\ClassName $className)
    {
    }

    public static function create(AttributeCollector\Name\ClassName $className): self
    {
        return new self($className);
    }

    public function className(): AttributeCollector\Name\ClassName
    {
        return $this->className;
    }

    public function equals(Location $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->className->equals($other->className);
    }
}
