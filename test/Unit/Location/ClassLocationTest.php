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

namespace Ergebnis\AttributeCollector\Test\Unit\Location;

use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Location\ClassLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 */
final class ClassLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class);

        $location = Location\ClassLocation::create($className);

        self::assertSame($className, $location->className());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = Location\ClassLocation::create(Name\ClassName::fromString(self::class));
        $two = $this->createStub(Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = Location\ClassLocation::create(Name\ClassName::fromString(self::class));
        $two = Location\ClassLocation::create(Name\ClassName::fromString(parent::class));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesAreEqual(): void
    {
        $one = Location\ClassLocation::create(Name\ClassName::fromString(self::class));
        $two = Location\ClassLocation::create(Name\ClassName::fromString(self::class));

        self::assertTrue($one->equals($two));
    }
}
