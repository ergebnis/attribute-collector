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

namespace Ergebnis\AttributeCollector\Collector;

use Ergebnis\AttributeCollector\Location\Location;

/**
 * @internal
 */
final class VisitedLocationCollection
{
    /**
     * @var list<Location>
     */
    private array $visitedLocations = [];

    public function add(Location $location): void
    {
        $this->visitedLocations[] = $location;
    }

    public function has(Location $location): bool
    {
        return \in_array($location, $this->visitedLocations, false);
    }
}
