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
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Location\FunctionParameterLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\FunctionName
 * @uses \Ergebnis\AttributeCollector\Name\ParameterName
 */
final class FunctionParameterLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $functionName = Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\foo');
        $parameterName = Name\ParameterName::fromString('bar');

        $location = Location\FunctionParameterLocation::create(
            $functionName,
            $parameterName,
        );

        self::assertSame($functionName, $location->functionName());
        self::assertSame($parameterName, $location->parameterName());
    }

    public function testEqualsReturnsFalseWhenTypesAreDifferent(): void
    {
        $one = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = $this->createStub(Location\Location::class);

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenFunctionNamesAreDifferent(): void
    {
        $one = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('baz'),
            Name\ParameterName::fromString('bar'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenParameterNamesAreDifferent(): void
    {
        $one = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('foo'),
            Name\ParameterName::fromString('baz'),
        );

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenFunctionNamesAndParameterNamesAreEqual(): void
    {
        $one = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );
        $two = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('foo'),
            Name\ParameterName::fromString('bar'),
        );

        self::assertTrue($one->equals($two));
    }
}
