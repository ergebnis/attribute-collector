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

namespace Ergebnis\AttributeCollector\Test\Unit;

use Ergebnis\AttributeCollector;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\AttributeCollection
 *
 * @uses \Ergebnis\AttributeCollector\Attribute
 * @uses \Ergebnis\AttributeCollector\Location\ClassConstantLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassPropertyLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 * @uses \Ergebnis\AttributeCollector\Name\PropertyName
 */
final class AttributeCollectionTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    public function testCreateReturnsCollectionWhenAttributesAreEmpty(): void
    {
        $collection = AttributeCollector\AttributeCollection::create();

        self::assertSame([], $collection->toArray());
    }

    public function testCreateReturnsCollectionWhenAttributesAreListOfAttributes(): void
    {
        $attributes = [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributeWithoutParameters::class)),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributeWithParameters::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'foo',
                    1,
                ),
            ),
        ];

        $collection = AttributeCollector\AttributeCollection::create(...$attributes);

        self::assertSame($attributes, $collection->toArray());
    }

    public function testCreateReturnsCollectionWhenAttributesAreArrayOfAttributes(): void
    {
        $attributes = [
            'foo' => AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributeWithoutParameters::class)),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            'bar' => AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributeWithParameters::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'foo',
                    1,
                ),
            ),
        ];

        $collection = AttributeCollector\AttributeCollection::create(...$attributes);

        self::assertSame(\array_values($attributes), $collection->toArray());
    }

    public function testWhereAttributeClassNameEqualsReturnsEmptyCollectionWhenAttributeCollectionIsEmpty(): void
    {
        $attributeClassName = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\AttributeWithoutParameters::class);

        $collection = AttributeCollector\AttributeCollection::create();

        $filteredCollection = $collection->whereAttributeClassNameEquals($attributeClassName);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame([], $filteredCollection->toArray());
    }

    public function testWhereAttributeClassNameEqualsReturnsEmptyCollectionWhenAttributeCollectionDoesNotContainAttributesWithClassName(): void
    {
        $attributeClassName = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\AttributeWithoutParameters::class);

        $collection = AttributeCollector\AttributeCollection::create(
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
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
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
        );

        $filteredCollection = $collection->whereAttributeClassNameEquals($attributeClassName);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame([], $filteredCollection->toArray());
    }

    public function testWhereAttributeClassNameEqualsReturnsCollectionWhereAttributeClassNamesAreEqual(): void
    {
        $attributeClassName = AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\AttributeWithParameters::class);

        $collection = AttributeCollector\AttributeCollection::create(
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
        );

        $filteredCollection = $collection->whereAttributeClassNameEquals($attributeClassName);

        self::assertNotSame($collection, $filteredCollection);

        $expectedAttributes = [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
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
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
        ];

        self::assertEquals($expectedAttributes, $filteredCollection->toArray());
    }

    public function testWhereAttributeLocationEqualsReturnsEmptyCollectionWhenAttributeCollectionIsEmpty(): void
    {
        $attributeLocation = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
        );

        $collection = AttributeCollector\AttributeCollection::create();

        $filteredCollection = $collection->whereAttributeLocationEquals($attributeLocation);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame([], $filteredCollection->toArray());
    }

    public function testWhereAttributeLocationEqualsReturnsEmptyCollectionWhenAttributeCollectionDoesNotContainAttributesWithLocation(): void
    {
        $attributeLocation = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );

        $collection = AttributeCollector\AttributeCollection::create(
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
        );

        $filteredCollection = $collection->whereAttributeLocationEquals($attributeLocation);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame([], $filteredCollection->toArray());
    }

    public function testWhereAttributeLocationEqualsReturnsCollectionWhereAttributeLocationsAreEqual(): void
    {
        $attributeLocation = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );

        $collection = AttributeCollector\AttributeCollection::create(
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
        );

        $filteredCollection = $collection->whereAttributeLocationEquals($attributeLocation);

        self::assertNotSame($collection, $filteredCollection);

        $expectedAttributes = [
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
        ];

        self::assertEquals($expectedAttributes, $filteredCollection->toArray());
    }
}
