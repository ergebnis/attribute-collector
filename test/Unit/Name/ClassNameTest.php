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
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Name\ClassName
 *
 * @uses \Ergebnis\AttributeCollector\Exception\InvalidClassName
 */
final class ClassNameTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidClassName::class);

        Name\ClassName::fromString($value);
    }

    public function testFromStringReturnsClassName(): void
    {
        $value = self::class;

        $className = Name\ClassName::fromString($value);

        self::assertSame($value, $className->toString());
    }
}
