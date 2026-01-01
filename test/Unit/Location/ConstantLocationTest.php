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
 * @covers \Ergebnis\AttributeCollector\Location\ConstantLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 */
final class ConstantLocationTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsLocation(): void
    {
        $constantName = Name\ConstantName::fromString(self::faker()->word());

        $location = Location\ConstantLocation::create($constantName);

        self::assertSame($constantName, $location->constantName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = Location\ConstantLocation::create(Name\ConstantName::fromString('FOO'));
        $two = $this->createStub(Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenConstantNamesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = Location\ConstantLocation::create(Name\ConstantName::fromString($faker->word()));
        $two = Location\ConstantLocation::create(Name\ConstantName::fromString($faker->word()));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenConstantNamesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = Location\ConstantLocation::create(Name\ConstantName::fromString($value));
        $two = Location\ConstantLocation::create(Name\ConstantName::fromString($value));

        self::assertTrue($one->equals($two));
    }
}
