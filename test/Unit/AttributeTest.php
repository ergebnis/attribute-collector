<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/attribute-collector
 */

namespace Ergebnis\AttributeCollector\Test\Unit;

use Ergebnis\AttributeCollector\Attribute;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Attribute
 *
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 */
final class AttributeTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsAttribute(): void
    {
        $faker = self::faker();

        $instance = new Test\Fixture\AttributeWithParameters(
            $faker->word(),
            $faker->numberBetween(1),
        );
        $location = Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithParameters::class));

        $attribute = Attribute::create(
            $location,
            $instance,
        );

        self::assertEquals(Name\ClassName::fromString($instance::class), $attribute->className());
        self::assertSame($location, $attribute->location());
        self::assertNotSame($instance, $attribute->instance());
        self::assertEquals($instance, $attribute->instance());
    }
}
