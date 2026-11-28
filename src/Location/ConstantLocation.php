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

final class ConstantLocation implements Location
{
    private function __construct(private AttributeCollector\Name\ConstantName $constantName)
    {
    }

    public static function create(AttributeCollector\Name\ConstantName $constantName): self
    {
        return new self($constantName);
    }

    public function constantName(): AttributeCollector\Name\ConstantName
    {
        return $this->constantName;
    }

    public function equals(Location $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        return $this->constantName->equals($other->constantName);
    }
}
