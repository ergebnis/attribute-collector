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
 * @covers \Ergebnis\AttributeCollector\Exception\ClassCouldNotBeReflected
 *
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 */
final class ClassCouldNotBeReflectedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForClassLocationWithReflectionExceptionReturnsException(): void
    {
        $classLocation = Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class));
        $reflectionException = new \ReflectionException(self::faker()->sentence());

        $exception = Exception\ClassCouldNotBeReflected::forClassLocationWithReflectionException(
            $classLocation,
            $reflectionException,
        );

        $message = \sprintf(
            'Class "%s" could not be reflected because a reflection exception was thrown with message "%s".',
            $classLocation->className()->toString(),
            $reflectionException->getMessage(),
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($reflectionException, $exception->getPrevious());
    }
}
