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
 * @covers \Ergebnis\AttributeCollector\Location\ClassConstantLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 */
final class ClassConstantLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class);
        $constantName = Name\ConstantName::fromString('FOO');

        $location = Location\ClassConstantLocation::create(
            $className,
            $constantName,
        );

        self::assertSame($className, $location->className());
        self::assertSame($constantName, $location->constantName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(self::class),
            Name\ConstantName::fromString('FOO'),
        );
        $two = $this->createStub(Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(self::class),
            Name\ConstantName::fromString('FOO'),
        );
        $two = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(parent::class),
            Name\ConstantName::fromString('FOO'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenConstantNamesAreDifferent(): void
    {
        $one = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(self::class),
            Name\ConstantName::fromString('FOO'),
        );
        $two = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(self::class),
            Name\ConstantName::fromString('BAR'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $one = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(self::class),
            Name\ConstantName::fromString('FOO'),
        );
        $two = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(self::class),
            Name\ConstantName::fromString('FOO'),
        );

        self::assertTrue($one->equals($two));
    }
}
