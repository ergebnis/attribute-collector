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

namespace Ergebnis\AttributeCollector\Test\Fixture;

final class ClassNotUsingAttributes
{
    public const FOO = 'bar';
    public string $fooBar = 'bar';

    public function barBaz(
        string $bazQux,
        int $quxQuux,
    ): void {
    }
}
