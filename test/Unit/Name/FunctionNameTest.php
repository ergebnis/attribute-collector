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
 * @covers \Ergebnis\AttributeCollector\Name\FunctionName
 *
 * @uses \Ergebnis\AttributeCollector\Exception\InvalidFunctionName
 */
final class FunctionNameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidFunctionName::class);

        Name\FunctionName::fromString($value);
    }

    public function testFromStringReturnsFunctionName(): void
    {
        $value = self::faker()->word();

        $functionName = Name\FunctionName::fromString($value);

        self::assertSame($value, $functionName->toString());
    }

    public function testEqualsReturnsFalseWhenValuesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = Name\FunctionName::fromString($faker->word());
        $two = Name\FunctionName::fromString($faker->word());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = Name\FunctionName::fromString($value);
        $two = Name\FunctionName::fromString($value);

        self::assertTrue($one->equals($two));
    }
}
