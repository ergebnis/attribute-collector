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

namespace Ergebnis\AttributeCollector\Test\Unit\Collector;

use Ergebnis\AttributeCollector\Attribute;
use Ergebnis\AttributeCollector\Collector;
use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Collector\TraversingAttributeFromClassNameCollector
 *
 * @uses \Ergebnis\AttributeCollector\Attribute
 * @uses \Ergebnis\AttributeCollector\AttributeCollection
 * @uses \Ergebnis\AttributeCollector\Collector\VisitedLocationCollection
 * @uses \Ergebnis\AttributeCollector\Exception\ClassCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Location\ClassConstantLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodParameterLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassPropertyLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 * @uses \Ergebnis\AttributeCollector\Name\ParameterName
 * @uses \Ergebnis\AttributeCollector\Name\PropertyName
 */
final class TraversingAttributeFromClassNameCollectorTest extends Framework\TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../Fixture/ClassUsingAttributes.php';
    }

    public function testCollectFromClassNameReturnsEmptyAttributeCollectionWhenClassNamesAreEmpty(): void
    {
        $collector = new Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName();

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromClassNameThrowsClassCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassName(): void
    {
        $className = Name\ClassName::fromString('UndefinedClass');

        $collector = new Collector\TraversingAttributeFromClassNameCollector();

        $this->expectException(Exception\ClassCouldNotBeReflected::class);

        $collector->collectFromClassName($className);
    }

    public function testCollectFromClassNameReturnsEmptyAttributeCollectionWhenClassNameIsForClassNotUsingAttributes(): void
    {
        $className = Name\ClassName::fromString(Test\Fixture\ClassNotUsingAttributes::class);

        $collector = new Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName($className);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromClassNameReturnsAttributeCollectionWhenLocationIsClassLocationForClassUsingAttributes(): void
    {
        $className = Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class);

        $collector = new Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName($className);

        $expected = self::attributesFromClassNameForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromClassNameReturnsAttributeCollectionWhenClassNamesContainDuplicateClassNamesForClassUsingAttributes(): void
    {
        $classNames = [
            Name\ClassName::fromString(\strtolower(Test\Fixture\ClassUsingAttributes::class)),
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\ClassName::fromString(\strtoupper(Test\Fixture\ClassUsingAttributes::class)),
        ];

        $collector = new Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName(...$classNames);

        $expected = self::attributesFromClassNameForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromClassNameForClassUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }
}
