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
 * @covers \Ergebnis\AttributeCollector\Name\MethodName
 *
 * @uses \Ergebnis\AttributeCollector\Exception\InvalidMethodName
 */
final class MethodNameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidMethodName::class);

        Name\MethodName::fromString($value);
    }

    public function testFromStringReturnsMethodName(): void
    {
        $value = self::faker()->word();

        $methodName = Name\MethodName::fromString($value);

        self::assertSame($value, $methodName->toString());
    }

    public function testEqualsReturnsFalseWhenValuesAreDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = Name\MethodName::fromString($faker->word());
        $two = Name\MethodName::fromString($faker->word());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $value = self::faker()->word();

        $one = Name\MethodName::fromString($value);
        $two = Name\MethodName::fromString($value);

        self::assertTrue($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqualWithDifferentCase(): void
    {
        $one = Name\MethodName::fromString('FoO');
        $two = Name\MethodName::fromString('fOo');

        self::assertTrue($one->equals($two));
    }
}
