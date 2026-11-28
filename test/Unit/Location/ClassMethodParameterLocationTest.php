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
        $className = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class);
        $methodName = AttributeCollector\Name\MethodName::fromString('barBaz');
        $parameterName = AttributeCollector\Name\ParameterName::fromString('bazQux');

        $location = AttributeCollector\Location\ClassMethodParameterLocation::create(
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
        $one = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );
        $two = $this->createStub(AttributeCollector\Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenClassNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );
        $two = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(parent::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenMethodNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );
        $two = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('bazQux'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenParameterNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );
        $two = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('bazQux'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenClassNamesMethodNamesAndPropertyNamesAreEqual(): void
    {
        $one = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );
        $two = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(self::class),
            AttributeCollector\Name\MethodName::fromString('fooBar'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );

        self::assertTrue($one->equals($two));
    }
}
