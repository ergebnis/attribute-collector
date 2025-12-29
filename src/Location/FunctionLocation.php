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

final class FunctionLocation implements Location
{
    private function __construct(private Name\FunctionName $functionName)
    {
    }

    public static function create(Name\FunctionName $functionName): self
    {
        return new self($functionName);
    }

    public function functionName(): Name\FunctionName
    {
        return $this->functionName;
    }
}
