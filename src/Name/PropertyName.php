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

namespace Ergebnis\AttributeCollector\Name;

use Ergebnis\AttributeCollector\Exception;

final class PropertyName
{
    private function __construct(private string $value)
    {
    }

    /**
     * @throws Exception\InvalidClassPropertyName
     */
    public static function fromString(string $value): self
    {
        if ('' === \trim($value)) {
            throw Exception\InvalidClassPropertyName::blankOrEmpty();
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
