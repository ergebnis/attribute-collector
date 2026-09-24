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

use Ergebnis\AttributeCollector\Location;

/**
 * @internal
 */
final class VisitedLocationCollection
{
    /**
     * @var list<Location\Location>
     */
    private array $visitedLocations = [];

    public function add(Location\Location $location): void
    {
        $this->visitedLocations[] = $location;
    }

    public function has(Location\Location $location): bool
    {
        foreach ($this->visitedLocations as $visitedLocation) {
            if ($visitedLocation->equals($location)) {
                return true;
            }
        }

        return false;
    }
}
