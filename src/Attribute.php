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

namespace Ergebnis\AttributeCollector;

final class Attribute
{
    private function __construct(
        private Name\ClassName $className,
        private Location\Location $target,
        private object $instance,
    ) {
    }

    public static function create(
        Location\Location $target,
        object $instance,
    ): self {
        return new self(
            Name\ClassName::fromString($instance::class),
            $target,
            $instance,
        );
    }

    public function className(): Name\ClassName
    {
        return $this->className;
    }

    public function location(): Location\Location
    {
        return $this->target;
    }

    public function instance(): object
    {
        return clone $this->instance;
    }
}
