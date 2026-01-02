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
 * @covers \Ergebnis\AttributeCollector\Exception\ClassMethodCouldNotBeReflected
 *
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 */
final class ClassMethodCouldNotBeReflectedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForClassMethodLocationWithReflectionExceptionReturnsException(): void
    {
        $classMethodLocation = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\MethodName::fromString('bazQux'),
        );
        $reflectionException = new \ReflectionException(self::faker()->sentence());

        $exception = Exception\ClassMethodCouldNotBeReflected::forClassMethodLocationWithReflectionException(
            $classMethodLocation,
            $reflectionException,
        );

        $message = \sprintf(
            'Class method "%s::%s()" could not be reflected because a reflection exception was thrown with message "%s".',
            $classMethodLocation->className()->toString(),
            $classMethodLocation->methodName()->toString(),
            $reflectionException->getMessage(),
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($reflectionException, $exception->getPrevious());
    }
}
