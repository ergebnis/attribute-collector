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

namespace Ergebnis\AttributeCollector\Test\Unit\Exception;

use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Exception\FunctionDoesNotHaveParameter
 *
 * @uses \Ergebnis\AttributeCollector\Location\FunctionParameterLocation
 * @uses \Ergebnis\AttributeCollector\Name\FunctionName
 * @uses \Ergebnis\AttributeCollector\Name\ParameterName
 */
final class FunctionDoesNotHaveParameterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForFunctionParameterLocationReturnsException(): void
    {
        $functionParameterLocation = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
            Name\ParameterName::fromString('corgeGrault'),
        );

        $exception = Exception\FunctionDoesNotHaveParameter::forFunctionParameterLocation($functionParameterLocation);

        $message = \sprintf(
            'Function "%s()" does not have a parameter "$%s".',
            $functionParameterLocation->functionName()->toString(),
            $functionParameterLocation->parameterName()->toString(),
        );

        self::assertSame($message, $exception->getMessage());
    }
}
