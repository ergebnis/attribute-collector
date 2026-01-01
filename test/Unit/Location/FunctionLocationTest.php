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
 * @covers \Ergebnis\AttributeCollector\Location\FunctionLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\FunctionName
 */
final class FunctionLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $functionName = Name\FunctionName::fromString('Ergebnis\\AttributeCollector\\Test\\Fixture\\foo');

        $location = Location\FunctionLocation::create($functionName);

        self::assertSame($functionName, $location->functionName());
    }
}
