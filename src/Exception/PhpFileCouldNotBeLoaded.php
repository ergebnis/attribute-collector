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

final class PhpFileCouldNotBeLoaded extends \RuntimeException implements Exception
{
    public static function forFileNameThatDoesNotExist(string $fileName): self
    {
        return new self(\sprintf(
            'PHP file "%s" could not be loaded because it does not exist.',
            $fileName,
        ));
    }

    public static function forFileNameThatIsNotReadable(string $fileName): self
    {
        return new self(\sprintf(
            'PHP file "%s" could not be loaded because it is not readable.',
            $fileName,
        ));
    }

    public static function forFileNameThatDoesNotReturnAttributeCollection(string $fileName): self
    {
        return new self(\sprintf(
            'PHP file "%s" could not be loaded because it does not return an AttributeCollection.',
            $fileName,
        ));
    }
}
