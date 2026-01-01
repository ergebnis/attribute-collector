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

use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Name\ParameterName
 *
 * @uses \Ergebnis\AttributeCollector\Exception\InvalidParameterName
 */
final class ParameterNameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidParameterName::class);

        Name\ParameterName::fromString($value);
    }

    public function testFromStringReturnsParameterName(): void
    {
        $value = self::faker()->word();

        $parameterName = Name\ParameterName::fromString($value);

        self::assertSame($value, $parameterName->toString());
    }

    public function testEqualsReturnsFalseWhenValuesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = Name\ParameterName::fromString($faker->word());
        $two = Name\ParameterName::fromString($faker->word());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = Name\ParameterName::fromString($value);
        $two = Name\ParameterName::fromString($value);

        self::assertTrue($one->equals($two));
    }
}
