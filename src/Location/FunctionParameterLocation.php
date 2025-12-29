<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/attribute-collector
 */

namespace Ergebnis\AttributeCollector\Location;

use Ergebnis\AttributeCollector\Name;

final class FunctionParameterLocation implements Location
{
    private function __construct(
        private Name\FunctionName $functionName,
        private Name\ParameterName $parameterName,
    ) {
    }

    public static function create(
        Name\FunctionName $functionName,
        Name\ParameterName $parameterName,
    ): self {
        return new self(
            $functionName,
            $parameterName,
        );
    }

    public function functionName(): Name\FunctionName
    {
        return $this->functionName;
    }

    public function parameterName(): Name\ParameterName
    {
        return $this->parameterName;
    }
}
