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
use Ergebnis\AttributeCollector\Location;

interface AttributeFromLocationCollector
{
    /**
     * @throws Exception\AttributeCollectionNotSupported
     * @throws Exception\ClassConstantCouldNotBeReflected
     * @throws Exception\ClassCouldNotBeReflected
     * @throws Exception\ClassMethodCouldNotBeReflected
     * @throws Exception\ClassMethodDoesNotHaveParameter
     * @throws Exception\ClassPropertyCouldNotBeReflected
     * @throws Exception\ConstantCouldNotBeReflected
     * @throws Exception\FunctionCouldNotBeReflected
     * @throws Exception\FunctionDoesNotHaveParameter
     */
    public function collectFromLocation(Location\Location ...$locations): AttributeCollection;
}
