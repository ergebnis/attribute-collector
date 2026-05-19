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
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Exception\AttributeInstanceCouldNotBeExported
 */
final class AttributeInstanceCouldNotBeExportedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForAttributeInstanceWithUnsupportedParameterValueTypeReturnsException(): void
    {
        $instance = new Test\Fixture\AttributeWithParameters(
            'foo',
            123,
        );

        $parameterName = self::faker()->word();
        $valueType = self::faker()->word();

        $exception = Exception\AttributeInstanceCouldNotBeExported::forAttributeInstanceWithUnsupportedParameterValueType(
            $instance,
            $parameterName,
            $valueType,
        );

        $message = \sprintf(
            'Attribute instance of class "%s" could not be exported because the value of constructor parameter "$%s" has the unsupported type "%s".',
            $instance::class,
            $parameterName,
            $valueType,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
