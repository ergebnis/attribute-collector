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

namespace Ergebnis\AttributeCollector\Collector;

use Ergebnis\AttributeCollector;
use Ergebnis\Classy;

final class TraversingAttributeFromFinderCollector implements AttributeFromFinderCollector
{
    public function collectFromFinder(iterable $finder): AttributeCollector\AttributeCollection
    {
        $constructFromFinderCollector = new Classy\Collector\DefaultConstructFromFinderCollector(new Classy\Collector\TokenGetAllConstructFromSourceCollector());

        try {
            $constructsFromSplFileInfo = $constructFromFinderCollector->collectFromFinder($finder);
        } catch (Classy\Exception\FileCouldNotBeParsed $fileCouldNotBeParsed) {
            throw AttributeCollector\Exception\FinderContainsFileThatCouldNotBeParsed::withMessage($fileCouldNotBeParsed->getMessage());
        }

        $classNames = \array_map(static function (Classy\ConstructFromSplFileInfo $construct): AttributeCollector\Name\ClassName {
            return AttributeCollector\Name\ClassName::fromString($construct->name()->toString());
        }, $constructsFromSplFileInfo);

        $traversingAttributeFromClassNameCollector = new TraversingAttributeFromClassNameCollector();

        return $traversingAttributeFromClassNameCollector->collectFromClassName(...$classNames);
    }
}
