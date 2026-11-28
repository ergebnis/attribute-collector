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
 * @covers \Ergebnis\AttributeCollector\Name\FunctionName
 *
 * @uses \Ergebnis\AttributeCollector\Exception\InvalidFunctionName
 */
final class FunctionNameTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(AttributeCollector\Exception\InvalidFunctionName::class);

        AttributeCollector\Name\FunctionName::fromString($value);
    }

    public function testFromStringReturnsFunctionName(): void
    {
        $value = self::faker()->word();

        $functionName = AttributeCollector\Name\FunctionName::fromString($value);

        self::assertSame($value, $functionName->toString());
    }

    public function testEqualsReturnsFalseWhenValuesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = AttributeCollector\Name\FunctionName::fromString($faker->word());
        $two = AttributeCollector\Name\FunctionName::fromString($faker->word());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = AttributeCollector\Name\FunctionName::fromString($value);
        $two = AttributeCollector\Name\FunctionName::fromString($value);

        self::assertTrue($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqualWithDifferentCase(): void
    {
        $one = AttributeCollector\Name\FunctionName::fromString('FoO');
        $two = AttributeCollector\Name\FunctionName::fromString('fOo');

        self::assertTrue($one->equals($two));
    }
}
