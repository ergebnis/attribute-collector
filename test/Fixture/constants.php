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
    bar: 123,
)]
#[AttributeWithoutParameters()]
const FOO = 'bar';

#[AttributeWithParameters(
    foo: 'bar',
    bar: 234,
)]
#[AttributeWithoutParameters()]
const BAR = 'baz';

const BAZ = 'qux';
