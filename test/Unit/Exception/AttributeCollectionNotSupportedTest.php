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
 * @covers \Ergebnis\AttributeCollector\Exception\AttributeCollectionNotSupported
 */
final class AttributeCollectionNotSupportedTest extends Framework\TestCase
{
    public function testForLocationReturnsException(): void
    {
        $location = $this->createStub(AttributeCollector\Location\Location::class);

        $exception = AttributeCollector\Exception\AttributeCollectionNotSupported::forLocation($location);

        $message = \sprintf(
            'Attribute collection is not supported for locations of type "%s".',
            $location::class,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
