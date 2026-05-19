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

namespace Ergebnis\AttributeCollector\Dumper;

use Ergebnis\AttributeCollector\AttributeCollection;
use Ergebnis\AttributeCollector\Exception;

final class PhpFileLoader
{
    /**
     * @throws Exception\PhpFileCouldNotBeLoaded
     */
    public function load(string $fileName): AttributeCollection
    {
        if (!\is_file($fileName)) {
            throw Exception\PhpFileCouldNotBeLoaded::forFileNameThatDoesNotExist($fileName);
        }

        if (!\is_readable($fileName)) {
            throw Exception\PhpFileCouldNotBeLoaded::forFileNameThatIsNotReadable($fileName);
        }

        $result = require $fileName;

        if (!$result instanceof AttributeCollection) {
            throw Exception\PhpFileCouldNotBeLoaded::forFileNameThatDoesNotReturnAttributeCollection($fileName);
        }

        return $result;
    }
}
