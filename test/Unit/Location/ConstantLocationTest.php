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
 * @covers \Ergebnis\AttributeCollector\Location\ConstantLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 */
final class ConstantLocationTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    public function testCreateReturnsLocation(): void
    {
        $constantName = AttributeCollector\Name\ConstantName::fromString(self::faker()->word());

        $location = AttributeCollector\Location\ConstantLocation::create($constantName);

        self::assertSame($constantName, $location->constantName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('FOO'));
        $two = $this->createStub(AttributeCollector\Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenConstantNamesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString($faker->word()));
        $two = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString($faker->word()));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenConstantNamesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString($value));
        $two = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString($value));

        self::assertTrue($one->equals($two));
    }
}
