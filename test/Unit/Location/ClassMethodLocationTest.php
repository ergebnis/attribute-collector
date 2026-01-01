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
 * @covers \Ergebnis\AttributeCollector\Location\ClassMethodLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 */
final class ClassMethodLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class);
        $methodName = Name\MethodName::fromString('foo');

        $location = Location\ClassMethodLocation::create(
            $className,
            $methodName,
        );

        self::assertSame($className, $location->className());
        self::assertSame($methodName, $location->methodName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
        );
        $two = $this->createStub(Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
        );
        $two = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(parent::class),
            Name\MethodName::fromString('foo'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenMethodNamesAreDifferent(): void
    {
        $one = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
        );
        $two = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('bar'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesAndMethodNamesAreEqual(): void
    {
        $one = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
        );
        $two = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
        );

        self::assertTrue($one->equals($two));
    }
}
