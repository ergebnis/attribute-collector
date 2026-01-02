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
 * @covers \Ergebnis\AttributeCollector\Location\ClassMethodParameterLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 * @uses \Ergebnis\AttributeCollector\Name\ParameterName
 */
final class ClassMethodParameterLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class);
        $methodName = Name\MethodName::fromString('barBaz');
        $parameterName = Name\ParameterName::fromString('bazQux');

        $location = Location\ClassMethodParameterLocation::create(
            $className,
            $methodName,
            $parameterName,
        );

        self::assertSame($className, $location->className());
        self::assertSame($methodName, $location->methodName());
        self::assertSame($parameterName, $location->parameterName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('barBaz'),
        );
        $two = $this->createStub(Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('barBaz'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(parent::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('barBaz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenMethodNamesAreDifferent(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('barBaz'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('bazQux'),
            Name\ParameterName::fromString('barBaz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenParameterNamesAreDifferent(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('barBaz'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('bazQux'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesMethodNamesAndPropertyNamesAreEqual(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('barBaz'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('fooBar'),
            Name\ParameterName::fromString('barBaz'),
        );

        self::assertTrue($one->equals($two));
    }
}
