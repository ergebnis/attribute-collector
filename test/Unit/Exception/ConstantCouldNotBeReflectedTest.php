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
 * @covers \Ergebnis\AttributeCollector\Exception\ConstantCouldNotBeReflected
 *
 * @uses \Ergebnis\AttributeCollector\Location\ConstantLocation
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 */
final class ConstantCouldNotBeReflectedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForConstantLocationWithReflectionExceptionReturnsException(): void
    {
        $constantLocation = Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO'));
        $reflectionException = new \ReflectionException(self::faker()->sentence());

        $exception = Exception\ConstantCouldNotBeReflected::forConstantLocationWithReflectionException(
            $constantLocation,
            $reflectionException,
        );

        $message = \sprintf(
            'Constant "%s" could not be reflected because a reflection exception was thrown with message "%s".',
            $constantLocation->constantName()->toString(),
            $reflectionException->getMessage(),
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($reflectionException, $exception->getPrevious());
    }
}
