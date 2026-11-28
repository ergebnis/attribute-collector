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

use Ergebnis\AttributeCollector;
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
        $collector = new AttributeCollector\Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName();

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromClassNameThrowsClassCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassName(): void
    {
        $className = AttributeCollector\Name\ClassName::fromString('UndefinedClass');

        $collector = new AttributeCollector\Collector\TraversingAttributeFromClassNameCollector();

        $this->expectException(AttributeCollector\Exception\ClassCouldNotBeReflected::class);

        $collector->collectFromClassName($className);
    }

    public function testCollectFromClassNameReturnsEmptyAttributeCollectionWhenClassNameIsForClassNotUsingAttributes(): void
    {
        $className = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassNotUsingAttributes::class);

        $collector = new AttributeCollector\Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName($className);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromClassNameReturnsAttributeCollectionWhenLocationIsClassLocationForClassUsingAttributes(): void
    {
        $className = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class);

        $collector = new AttributeCollector\Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName($className);

        $expected = self::attributesFromClassNameForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromClassNameReturnsAttributeCollectionWhenClassNamesContainDuplicateClassNamesForClassUsingAttributes(): void
    {
        $classNames = [
            AttributeCollector\Name\ClassName::fromString(\strtolower(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\ClassName::fromString(\strtoupper(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromClassNameCollector();

        $collection = $collector->collectFromClassName(...$classNames);

        $expected = self::attributesFromClassNameForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromClassNameForClassUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }
}
