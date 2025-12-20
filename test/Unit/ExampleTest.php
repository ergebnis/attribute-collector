<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/attribute-collector
 */

namespace Ergebnis\AttributeCollector\Test\Unit;

use Ergebnis\AttributeCollector\Example;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Example
 */
final class ExampleTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromStringReturnsExample(): void
    {
        $value = self::faker()->sentence();

        $example = Example::fromString($value);

        self::assertSame($value, $example->toString());
    }
}
