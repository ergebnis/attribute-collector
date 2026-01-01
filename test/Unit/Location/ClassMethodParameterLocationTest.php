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
        $methodName = Name\MethodName::fromString('foo');
        $parameterName = Name\ParameterName::fromString('bar');

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
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = $this->createStub(Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(parent::class),
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenMethodNamesAreDifferent(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('baz'),
            Name\ParameterName::fromString('bar'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenParameterNamesAreDifferent(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('baz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesMethodNamesAndPropertyNamesAreEqual(): void
    {
        $one = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(self::class),
            Name\MethodName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );

        self::assertTrue($one->equals($two));
    }
}
