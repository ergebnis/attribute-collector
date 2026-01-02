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

use Ergebnis\AttributeCollector\Attribute;
use Ergebnis\AttributeCollector\AttributeCollection;
use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;

final class TraversingAttributeFromClassNameCollector implements AttributeFromClassNameCollector
{
    public function collectFromClassName(Name\ClassName ...$classNames): AttributeCollection
    {
        $visitedLocationCollection = new VisitedLocationCollection();

        $attributes = [];

        foreach ($classNames as $className) {
            $classLocation = Location\ClassLocation::create($className);

            if ($visitedLocationCollection->has($classLocation)) {
                continue;
            }

            try {
                $reflectionClass = new \ReflectionClass($className->toString());
            } catch (\ReflectionException $reflectionException) {
                throw Exception\ClassCouldNotBeReflected::forClassLocationWithReflectionException(
                    $classLocation,
                    $reflectionException,
                );
            }

            $classLocation = Location\ClassLocation::create(Name\ClassName::fromString($reflectionClass->getName()));

            foreach ($reflectionClass->getAttributes() as $reflectionAttributeLocatedOnClass) {
                $attributes[] = Attribute::create(
                    $classLocation,
                    $reflectionAttributeLocatedOnClass->newInstance(),
                );
            }

            foreach ($reflectionClass->getReflectionConstants() as $reflectionConstant) {
                $classConstantLocation = Location\ClassConstantLocation::create(
                    $classLocation->className(),
                    Name\ConstantName::fromString($reflectionConstant->getName()),
                );

                foreach ($reflectionConstant->getAttributes() as $reflectionAttributeLocatedOnClassConstant) {
                    $attributes[] = Attribute::create(
                        $classConstantLocation,
                        $reflectionAttributeLocatedOnClassConstant->newInstance(),
                    );
                }
            }

            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                $classPropertyLocation = Location\ClassPropertyLocation::create(
                    $classLocation->className(),
                    Name\PropertyName::fromString($reflectionProperty->getName()),
                );

                foreach ($reflectionProperty->getAttributes() as $reflectionAttributeLocatedOnClassProperty) {
                    $attributes[] = Attribute::create(
                        $classPropertyLocation,
                        $reflectionAttributeLocatedOnClassProperty->newInstance(),
                    );
                }
            }

            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $classMethodLocation = Location\ClassMethodLocation::create(
                    $classLocation->className(),
                    Name\MethodName::fromString($reflectionMethod->getName()),
                );

                foreach ($reflectionMethod->getAttributes() as $reflectionAttributeLocatedOnMethod) {
                    $attributes[] = Attribute::create(
                        $classMethodLocation,
                        $reflectionAttributeLocatedOnMethod->newInstance(),
                    );
                }

                foreach ($reflectionMethod->getParameters() as $reflectionMethodParameter) {
                    $classMethodParameterLocation = Location\ClassMethodParameterLocation::create(
                        $classLocation->className(),
                        $classMethodLocation->methodName(),
                        Name\ParameterName::fromString($reflectionMethodParameter->getName()),
                    );

                    foreach ($reflectionMethodParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
                        $attributes[] = Attribute::create(
                            $classMethodParameterLocation,
                            $reflectionAttributeLocatedOnMethodParameter->newInstance(),
                        );
                    }
                }
            }

            $visitedLocationCollection->add($classLocation);
        }

        return AttributeCollection::create(...$attributes);
    }
}
