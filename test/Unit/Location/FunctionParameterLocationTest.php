<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025 Andreas Möller
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
}
