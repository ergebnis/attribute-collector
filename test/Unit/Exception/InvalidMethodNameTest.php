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
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Exception\InvalidMethodName
 */
final class InvalidMethodNameTest extends Framework\TestCase
{
    public function testBlankOrEmptyReturnsException(): void
    {
        $exception = Exception\InvalidMethodName::blankOrEmpty();

        self::assertSame('Method name cannot be blank or empty.', $exception->getMessage());
    }
}
