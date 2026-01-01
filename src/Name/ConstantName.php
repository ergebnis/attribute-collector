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

namespace Ergebnis\AttributeCollector\Name;

use Ergebnis\AttributeCollector\Exception;

final class ConstantName
{
    private function __construct(private string $value)
    {
    }

    /**
     * @throws Exception\InvalidConstantName
     */
    public static function fromString(string $value): self
    {
        if ('' === \trim($value)) {
            throw Exception\InvalidConstantName::blankOrEmpty();
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return \strtolower($this->value) === \strtolower($other->value);
    }
}
