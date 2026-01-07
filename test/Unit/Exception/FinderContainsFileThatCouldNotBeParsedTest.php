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
 * @covers \Ergebnis\AttributeCollector\Exception\FinderContainsFileThatCouldNotBeParsed
 */
final class FinderContainsFileThatCouldNotBeParsedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForFunctionParameterLocationReturnsException(): void
    {
        $message = self::faker()->sentence();

        $exception = Exception\FinderContainsFileThatCouldNotBeParsed::withMessage($message);

        self::assertSame($message, $exception->getMessage());
    }
}
