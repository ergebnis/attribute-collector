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

final class ClassMethodParameterLocation implements Location
{
    private function __construct(
        private Name\ClassName $className,
        private Name\MethodName $methodName,
        private Name\ParameterName $parameterName,
    ) {
    }

    public static function create(
        Name\ClassName $className,
        Name\MethodName $methodName,
        Name\ParameterName $parameterName,
    ): self {
        return new self(
            $className,
            $methodName,
            $parameterName,
        );
    }

    public function className(): Name\ClassName
    {
        return $this->className;
    }

    public function methodName(): Name\MethodName
    {
        return $this->methodName;
    }

    public function parameterName(): Name\ParameterName
    {
        return $this->parameterName;
    }
}
