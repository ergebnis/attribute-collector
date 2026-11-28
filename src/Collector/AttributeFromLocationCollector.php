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

interface AttributeFromLocationCollector
{
    /**
     * @throws AttributeCollector\Exception\AttributeCollectionNotSupported
     * @throws AttributeCollector\Exception\ClassConstantCouldNotBeReflected
     * @throws AttributeCollector\Exception\ClassCouldNotBeReflected
     * @throws AttributeCollector\Exception\ClassMethodCouldNotBeReflected
     * @throws AttributeCollector\Exception\ClassMethodDoesNotHaveParameter
     * @throws AttributeCollector\Exception\ClassPropertyCouldNotBeReflected
     * @throws AttributeCollector\Exception\ConstantCouldNotBeReflected
     * @throws AttributeCollector\Exception\FunctionCouldNotBeReflected
     * @throws AttributeCollector\Exception\FunctionDoesNotHaveParameter
     */
    public function collectFromLocation(AttributeCollector\Location\Location ...$locations): AttributeCollector\AttributeCollection;
}
