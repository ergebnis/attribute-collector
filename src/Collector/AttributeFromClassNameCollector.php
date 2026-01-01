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

use Ergebnis\AttributeCollector\AttributeCollection;
use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Name;

interface AttributeFromClassNameCollector
{
    /**
     * @throws Exception\ClassCouldNotBeReflected
     */
    public function collectFromClassName(Name\ClassName ...$classNames): AttributeCollection;
}
