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
 * @covers \Ergebnis\AttributeCollector\Attribute
 *
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 */
final class AttributeTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    public function testCreateReturnsAttribute(): void
    {
        $faker = self::faker();

        $instance = new AttributeCollector\Test\Fixture\AttributeWithParameters(
            $faker->word(),
            $faker->numberBetween(1),
        );
        $location = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributeWithParameters::class));

        $attribute = AttributeCollector\Attribute::create(
            $location,
            $instance,
        );

        self::assertEquals(AttributeCollector\Name\ClassName::fromString($instance::class), $attribute->className());
        self::assertSame($location, $attribute->location());
        self::assertNotSame($instance, $attribute->instance());
        self::assertEquals($instance, $attribute->instance());
    }
}
