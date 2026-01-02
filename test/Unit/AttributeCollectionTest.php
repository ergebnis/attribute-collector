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

use Ergebnis\AttributeCollector\Attribute;
use Ergebnis\AttributeCollector\AttributeCollection;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
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
    use Test\Util\Helper;

    public function testCreateReturnsCollectionWhenAttributesAreEmpty(): void
    {
        $collection = AttributeCollection::create();

        self::assertSame([], $collection->toArray());
    }

    public function testCreateReturnsCollectionWhenAttributesAreListOfAttributes(): void
    {
        $attributes = [
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithoutParameters::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithParameters::class)),
                new Test\Fixture\AttributeWithParameters(
                    'foo',
                    1,
                ),
            ),
        ];

        $collection = AttributeCollection::create(...$attributes);

        self::assertSame($attributes, $collection->toArray());
    }

    public function testCreateReturnsCollectionWhenAttributesAreArrayOfAttributes(): void
    {
        $attributes = [
            'foo' => Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithoutParameters::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            'bar' => Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithParameters::class)),
                new Test\Fixture\AttributeWithParameters(
                    'foo',
                    1,
                ),
            ),
        ];

        $collection = AttributeCollection::create(...$attributes);

        self::assertSame(\array_values($attributes), $collection->toArray());
    }

    public function testWhereAttributeClassNameEqualsReturnsEmptyCollectionWhenAttributeCollectionIsEmpty(): void
    {
        $attributeClassName = Name\ClassName::fromString(Test\Fixture\AttributeWithoutParameters::class);

        $collection = AttributeCollection::create();

        $filteredCollection = $collection->whereAttributeClassNameEquals($attributeClassName);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame([], $filteredCollection->toArray());
    }

    public function testWhereAttributeClassNameEqualsReturnsEmptyCollectionWhenAttributeCollectionDoesNotContainAttributesWithClassName(): void
    {
        $attributeClassName = Name\ClassName::fromString(Test\Fixture\AttributeWithoutParameters::class);

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
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
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithParameters(
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
        $attributeClassName = Name\ClassName::fromString(Test\Fixture\AttributeWithParameters::class);

        $collection = AttributeCollection::create(
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
        );

        $filteredCollection = $collection->whereAttributeClassNameEquals($attributeClassName);

        self::assertNotSame($collection, $filteredCollection);

        $expectedAttributes = [
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
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
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
        ];

        self::assertEquals($expectedAttributes, $filteredCollection->toArray());
    }

    public function testWhereAttributeLocationEqualsReturnsEmptyCollectionWhenAttributeCollectionIsEmpty(): void
    {
        $attributeLocation = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\MethodName::fromString('barBaz'),
        );

        $collection = AttributeCollection::create();

        $filteredCollection = $collection->whereAttributeLocationEquals($attributeLocation);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame([], $filteredCollection->toArray());
    }

    public function testWhereAttributeLocationEqualsReturnsEmptyCollectionWhenAttributeCollectionDoesNotContainAttributesWithLocation(): void
    {
        $attributeLocation = Location\ClassPropertyLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\PropertyName::fromString('fooBar'),
        );

        $collection = AttributeCollection::create(
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
        );

        $filteredCollection = $collection->whereAttributeLocationEquals($attributeLocation);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame([], $filteredCollection->toArray());
    }

    public function testWhereAttributeLocationEqualsReturnsCollectionWhereAttributeLocationsAreEqual(): void
    {
        $attributeLocation = Location\ClassPropertyLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\PropertyName::fromString('fooBar'),
        );

        $collection = AttributeCollection::create(
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
        );

        $filteredCollection = $collection->whereAttributeLocationEquals($attributeLocation);

        self::assertNotSame($collection, $filteredCollection);

        $expectedAttributes = [
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
        ];

        self::assertEquals($expectedAttributes, $filteredCollection->toArray());
    }
}
