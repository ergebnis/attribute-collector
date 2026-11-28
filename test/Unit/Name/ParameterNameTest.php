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
 * @covers \Ergebnis\AttributeCollector\Name\ParameterName
 *
 * @uses \Ergebnis\AttributeCollector\Exception\InvalidParameterName
 */
final class ParameterNameTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(AttributeCollector\Exception\InvalidParameterName::class);

        AttributeCollector\Name\ParameterName::fromString($value);
    }

    public function testFromStringReturnsParameterName(): void
    {
        $value = self::faker()->word();

        $parameterName = AttributeCollector\Name\ParameterName::fromString($value);

        self::assertSame($value, $parameterName->toString());
    }

    public function testEqualsReturnsFalseWhenValuesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = AttributeCollector\Name\ParameterName::fromString($faker->word());
        $two = AttributeCollector\Name\ParameterName::fromString($faker->word());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = AttributeCollector\Name\ParameterName::fromString($value);
        $two = AttributeCollector\Name\ParameterName::fromString($value);

        self::assertTrue($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqualWithDifferentCase(): void
    {
        $one = AttributeCollector\Name\ParameterName::fromString('FoO');
        $two = AttributeCollector\Name\ParameterName::fromString('fOo');

        self::assertTrue($one->equals($two));
    }
}
