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

final class ClassMethodLocation implements Location
{
    private function __construct(
        private AttributeCollector\Name\ClassName $className,
        private AttributeCollector\Name\MethodName $methodName,
    ) {
    }

    public static function create(
        AttributeCollector\Name\ClassName $className,
        AttributeCollector\Name\MethodName $methodName,
    ): self {
        return new self(
            $className,
            $methodName,
        );
    }

    public function className(): AttributeCollector\Name\ClassName
    {
        return $this->className;
    }

    public function methodName(): AttributeCollector\Name\MethodName
    {
        return $this->methodName;
    }

    public function equals(Location $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        if (!$this->className->equals($other->className)) {
            return false;
        }

        return $this->methodName->equals($other->methodName);
    }
}
