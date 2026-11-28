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

use Ergebnis\AttributeCollector;
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
        $className = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class);

        $location = AttributeCollector\Location\ClassLocation::create($className);

        self::assertSame($className, $location->className());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(self::class));
        $two = $this->createStub(AttributeCollector\Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(self::class));
        $two = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(parent::class));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesAreEqual(): void
    {
        $one = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(self::class));
        $two = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(self::class));

        self::assertTrue($one->equals($two));
    }
}
