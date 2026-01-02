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

#[AttributeWithParameters(
    foo: 'qux',
    bar: 234,
)]
#[AttributeWithoutParameters()]
function quuxCorge(
    #[AttributeWithParameters(
        foo: 'quz',
        bar: 345,
    )]
    #[AttributeWithoutParameters()]
    string $corgeGrault,
    int $graultGarply,
): void {
}

#[AttributeWithParameters(
    foo: 'quz',
    bar: 345,
)]
#[AttributeWithoutParameters()]
function garplyWaldo(
    #[AttributeWithoutParameters()]
    string $waldoFred,
    float $fredPlugh,
): void {
}

function plughXyzzy(
    string $xyzzyThud,
    int $thudFoo,
): void {
}
