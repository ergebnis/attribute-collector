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

use Ergebnis\AttributeCollector\Name;

final class ClassPropertyLocation implements Location
{
    private function __construct(
        private Name\ClassName $className,
        private Name\PropertyName $propertyName,
    ) {
    }

    public static function create(
        Name\ClassName $className,
        Name\PropertyName $propertyName,
    ): self {
        return new self(
            $className,
            $propertyName,
        );
    }

    public function className(): Name\ClassName
    {
        return $this->className;
    }

    public function propertyName(): Name\PropertyName
    {
        return $this->propertyName;
    }

    public function equals(Location $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        if (!$this->className->equals($other->className)) {
            return false;
        }

        if (!$this->propertyName->equals($other->propertyName)) {
            return false;
        }

        return true;
    }
}
