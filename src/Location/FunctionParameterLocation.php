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

final class FunctionParameterLocation implements Location
{
    private function __construct(
        private AttributeCollector\Name\FunctionName $functionName,
        private AttributeCollector\Name\ParameterName $parameterName,
    ) {
    }

    public static function create(
        AttributeCollector\Name\FunctionName $functionName,
        AttributeCollector\Name\ParameterName $parameterName,
    ): self {
        return new self(
            $functionName,
            $parameterName,
        );
    }

    public function functionName(): AttributeCollector\Name\FunctionName
    {
        return $this->functionName;
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

        if (!$this->functionName->equals($other->functionName)) {
            return false;
        }

        return $this->parameterName->equals($other->parameterName);
    }
}
