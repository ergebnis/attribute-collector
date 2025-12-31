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

namespace Ergebnis\AttributeCollector\Test\Unit\Collector;

use Ergebnis\AttributeCollector\Collector;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Collector\VisitedLocationCollection
 *
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 */
final class VisitedLocationCollectionTest extends Framework\TestCase
{
    public function testHasReturnsFalseWhenCollectionIsEmpty(): void
    {
        $location = Location\ClassLocation::create(Name\ClassName::fromString(\stdClass::class));

        $collection = new Collector\VisitedLocationCollection();

        self::assertFalse($collection->has($location));
    }

    public function testHasReturnsTrueWhenCollectionHasIdenticalLocation(): void
    {
        $location = Location\ClassLocation::create(Name\ClassName::fromString(\stdClass::class));

        $collection = new Collector\VisitedLocationCollection();

        $collection->add($location);

        self::assertTrue($collection->has($location));
    }

    public function testHasReturnsTrueWhenCollectionHasEqualLocation(): void
    {
        $one = Location\ClassLocation::create(Name\ClassName::fromString(\stdClass::class));
        $two = Location\ClassLocation::create(Name\ClassName::fromString(\stdClass::class));

        $collection = new Collector\VisitedLocationCollection();

        $collection->add($one);

        self::assertTrue($collection->has($two));
    }
}
