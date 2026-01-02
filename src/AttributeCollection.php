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

namespace Ergebnis\AttributeCollector;

final class AttributeCollection
{
    /**
     * @var list<Attribute>
     */
    private array $attributes;

    private function __construct(Attribute ...$attributes)
    {
        $this->attributes = \array_values($attributes);
    }

    public static function create(Attribute ...$attributes): self
    {
        return new self(...$attributes);
    }

    /**
     * @return list<Attribute>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function whereAttributeClassNameEquals(Name\ClassName $className): self
    {
        return new self(...\array_filter($this->attributes, static function (Attribute $attribute) use ($className): bool {
            return $attribute->className()->equals($className);
        }));
    }
}
