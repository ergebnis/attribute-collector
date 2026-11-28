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

use Ergebnis\AttributeCollector;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Exception\ClassMethodDoesNotHaveParameter
 *
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodParameterLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 * @uses \Ergebnis\AttributeCollector\Name\ParameterName
 */
final class ClassMethodDoesNotHaveParameterTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    public function testForClassMethodParameterLocationReturnsException(): void
    {
        $classMethodParameterLocation = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
            AttributeCollector\Name\ParameterName::fromString('bazQux'),
        );

        $exception = AttributeCollector\Exception\ClassMethodDoesNotHaveParameter::forClassMethodParameterLocation($classMethodParameterLocation);

        $message = \sprintf(
            'Class method "%s::%s()" does not have a parameter "$%s".',
            $classMethodParameterLocation->className()->toString(),
            $classMethodParameterLocation->methodName()->toString(),
            $classMethodParameterLocation->parameterName()->toString(),
        );

        self::assertSame($message, $exception->getMessage());
    }
}
