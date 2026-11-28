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
 * @covers \Ergebnis\AttributeCollector\Location\ClassConstantLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 */
final class ClassConstantLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class);
        $constantName = AttributeCollector\Name\ConstantName::fromString('FOO');

        $location = AttributeCollector\Location\ClassConstantLocation::create(
            $className,
            $constantName,
        );

        self::assertSame($className, $location->className());
        self::assertSame($constantName, $location->constantName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );
        $two = $this->createStub(AttributeCollector\Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );
        $two = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(parent::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenConstantNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );
        $two = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\ConstantName::fromString('BAR'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $one = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );
        $two = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );

        self::assertTrue($one->equals($two));
    }
}
