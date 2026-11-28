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
 * @covers \Ergebnis\AttributeCollector\Location\FunctionLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\FunctionName
 */
final class FunctionLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $functionName = AttributeCollector\Name\FunctionName::fromString('Ergebnis\\AttributeCollector\\Test\\Fixture\\quuxCorge');

        $location = AttributeCollector\Location\FunctionLocation::create($functionName);

        self::assertSame($functionName, $location->functionName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('fooBar'));
        $two = $this->createStub(AttributeCollector\Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenFunctionNamesAreDifferent(): void
    {
        $one = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('fooBar'));
        $two = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('barBaz'));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenFunctionNamesAreEqual(): void
    {
        $one = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('fooBar'));
        $two = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('fooBar'));

        self::assertTrue($one->equals($two));
    }
}
