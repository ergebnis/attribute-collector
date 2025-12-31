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

namespace Ergebnis\AttributeCollector\Exception;

use Ergebnis\AttributeCollector\Location;

final class FunctionDoesNotHaveParameter extends \RuntimeException implements Exception
{
    public static function forFunctionParameterLocation(Location\FunctionParameterLocation $functionParameterLocation): self
    {
        return new self(\sprintf(
            'Function "%s()" does not have a parameter "$%s".',
            $functionParameterLocation->functionName()->toString(),
            $functionParameterLocation->parameterName()->toString(),
        ));
    }
}
