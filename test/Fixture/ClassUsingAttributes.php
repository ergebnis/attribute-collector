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

namespace Ergebnis\AttributeCollector\Test\Fixture;

#[AttributeWithParameters(
    foo: 'bar',
    bar: 1,
)]
#[AttributeWithoutParameters()]
final class ClassUsingAttributes
{
    #[AttributeWithParameters(
        foo: 'bar',
        bar: 123,
    )]
    #[AttributeWithoutParameters()]
    public const FOO = 'bar';

    #[AttributeWithParameters(
        foo: 'bar',
        bar: 234,
    )]
    #[AttributeWithoutParameters()]
    public string $foo = 'bar';

    #[AttributeWithParameters(
        foo: 'bar',
        bar: 345,
    )]
    #[AttributeWithoutParameters()]
    public function foo(
        #[AttributeWithParameters(
            foo: 'bar',
            bar: 456,
        )]
        #[AttributeWithoutParameters()]
        string $bar,
        int $baz,
    ): void {
    }
}
