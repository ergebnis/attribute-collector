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
 * @covers \Ergebnis\AttributeCollector\Location\ClassPropertyLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\PropertyName
 */
final class ClassPropertyLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class);
        $propertyName = AttributeCollector\Name\PropertyName::fromString('fooBar');

        $location = AttributeCollector\Location\ClassPropertyLocation::create(
            $className,
            $propertyName,
        );

        self::assertSame($className, $location->className());
        self::assertSame($propertyName, $location->propertyName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );
        $two = $this->createStub(AttributeCollector\Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );
        $two = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(parent::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenPropertyNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );
        $two = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\PropertyName::fromString('barBaz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesAndPropertyNamesAreEqual(): void
    {
        $one = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );
        $two = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );

        self::assertTrue($one->equals($two));
    }
}
