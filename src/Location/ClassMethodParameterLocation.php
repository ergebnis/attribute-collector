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

final class ClassMethodParameterLocation implements Location
{
    private function __construct(
        private AttributeCollector\Name\ClassName $className,
        private AttributeCollector\Name\MethodName $methodName,
        private AttributeCollector\Name\ParameterName $parameterName,
    ) {
    }

    public static function create(
        AttributeCollector\Name\ClassName $className,
        AttributeCollector\Name\MethodName $methodName,
        AttributeCollector\Name\ParameterName $parameterName,
    ): self {
        return new self(
            $className,
            $methodName,
            $parameterName,
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

    public function parameterName(): AttributeCollector\Name\ParameterName
    {
        return $this->parameterName;
    }

    public function equals(Location $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        if (!$this->className->equals($other->className)) {
            return false;
        }

        if (!$this->methodName->equals($other->methodName)) {
            return false;
        }
        return $this->parameterName->equals($other->parameterName);
    }
}
