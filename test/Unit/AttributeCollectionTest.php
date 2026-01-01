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
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
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
}
