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

final class ClassLocation implements Location
{
    private function __construct(private Name\ClassName $className)
    {
    }

    public static function create(Name\ClassName $className): self
    {
        return new self($className);
    }

    public function className(): Name\ClassName
    {
        return $this->className;
    }
}
