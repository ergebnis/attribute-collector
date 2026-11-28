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

namespace Ergebnis\AttributeCollector\Test\Unit\Name;

use Ergebnis\AttributeCollector;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Name\PropertyName
 *
 * @uses \Ergebnis\AttributeCollector\Exception\InvalidPropertyName
 */
final class PropertyNameTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(AttributeCollector\Exception\InvalidPropertyName::class);

        AttributeCollector\Name\PropertyName::fromString($value);
    }

    public function testFromStringReturnsPropertyName(): void
    {
        $value = self::faker()->word();

        $propertyName = AttributeCollector\Name\PropertyName::fromString($value);

        self::assertSame($value, $propertyName->toString());
    }

    public function testEqualsReturnsFalseWhenValuesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = AttributeCollector\Name\PropertyName::fromString($faker->word());
        $two = AttributeCollector\Name\PropertyName::fromString($faker->word());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = AttributeCollector\Name\PropertyName::fromString($value);
        $two = AttributeCollector\Name\PropertyName::fromString($value);

        self::assertTrue($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqualWithDifferentCase(): void
    {
        $one = AttributeCollector\Name\PropertyName::fromString('FoO');
        $two = AttributeCollector\Name\PropertyName::fromString('fOo');

        self::assertTrue($one->equals($two));
    }
}
