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
 * @covers \Ergebnis\AttributeCollector\Location\ClassMethodLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 */
final class ClassMethodLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class);
        $methodName = AttributeCollector\Name\MethodName::fromString('barBaz');

        $location = AttributeCollector\Location\ClassMethodLocation::create(
            $className,
            $methodName,
        );

        self::assertSame($className, $location->className());
        self::assertSame($methodName, $location->methodName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
        );
        $two = $this->createStub(AttributeCollector\Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
        );
        $two = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(parent::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenMethodNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
        );
        $two = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesAndMethodNamesAreEqual(): void
    {
        $one = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
        );
        $two = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
        );

        self::assertTrue($one->equals($two));
    }
}
