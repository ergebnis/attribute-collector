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

namespace Ergebnis\AttributeCollector\Exception;

final class InvalidFunctionName extends \InvalidArgumentException implements Exception
{
    public static function blankOrEmpty(): self
    {
        return new self('Function name cannot be blank or empty.');
    }
}
