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
 * @covers \Ergebnis\AttributeCollector\Exception\FunctionCouldNotBeReflected
 *
 * @uses \Ergebnis\AttributeCollector\Location\FunctionLocation
 * @uses \Ergebnis\AttributeCollector\Name\FunctionName
 */
final class FunctionCouldNotBeReflectedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForFunctionLocationWithReflectionExceptionReturnsException(): void
    {
        $functionLocation = Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\foo'));
        $reflectionException = new \ReflectionException(self::faker()->sentence());

        $exception = Exception\FunctionCouldNotBeReflected::forFunctionLocationWithReflectionException(
            $functionLocation,
            $reflectionException,
        );

        $message = \sprintf(
            'Function "%s" could not be reflected because a reflection exception was thrown with message "%s".',
            $functionLocation->functionName()->toString(),
            $reflectionException->getMessage(),
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($reflectionException, $exception->getPrevious());
    }
}
