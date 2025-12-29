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

namespace Ergebnis\AttributeCollector\Test\Unit\Location;

use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Location\ClassPropertyLocation
 *
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\PropertyName
 */
final class ClassPropertyLocationTest extends Framework\TestCase
{
    public function testCreateReturnsLocation(): void
    {
        $className = Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class);
        $propertyName = Name\PropertyName::fromString('foo');

        $location = Location\ClassPropertyLocation::create(
            $className,
            $propertyName,
        );

        self::assertSame($className, $location->className());
        self::assertSame($propertyName, $location->propertyName());
    }
}
