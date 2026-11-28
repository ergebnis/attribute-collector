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

final class TraversingAttributeFromClassNameCollector implements AttributeFromClassNameCollector
{
    public function collectFromClassName(AttributeCollector\Name\ClassName ...$classNames): AttributeCollector\AttributeCollection
    {
        $visitedLocationCollection = new VisitedLocationCollection();

        $attributes = [];

        foreach ($classNames as $className) {
            $classLocation = AttributeCollector\Location\ClassLocation::create($className);

            if ($visitedLocationCollection->has($classLocation)) {
                continue;
            }

            try {
                $reflectionClass = new \ReflectionClass($className->toString());
            } catch (\ReflectionException $reflectionException) {
                throw AttributeCollector\Exception\ClassCouldNotBeReflected::forClassLocationWithReflectionException(
                    $classLocation,
                    $reflectionException,
                );
            }

            $classLocation = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString($reflectionClass->getName()));

            foreach ($reflectionClass->getAttributes() as $reflectionAttributeLocatedOnClass) {
                $attributes[] = AttributeCollector\Attribute::create(
                    $classLocation,
                    $reflectionAttributeLocatedOnClass->newInstance(),
                );
            }

            foreach ($reflectionClass->getReflectionConstants() as $reflectionConstant) {
                $classConstantLocation = AttributeCollector\Location\ClassConstantLocation::create(
                    $classLocation->className(),
                    AttributeCollector\Name\ConstantName::fromString($reflectionConstant->getName()),
                );

                foreach ($reflectionConstant->getAttributes() as $reflectionAttributeLocatedOnClassConstant) {
                    $attributes[] = AttributeCollector\Attribute::create(
                        $classConstantLocation,
                        $reflectionAttributeLocatedOnClassConstant->newInstance(),
                    );
                }
            }

            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                $classPropertyLocation = AttributeCollector\Location\ClassPropertyLocation::create(
                    $classLocation->className(),
                    AttributeCollector\Name\PropertyName::fromString($reflectionProperty->getName()),
                );

                foreach ($reflectionProperty->getAttributes() as $reflectionAttributeLocatedOnClassProperty) {
                    $attributes[] = AttributeCollector\Attribute::create(
                        $classPropertyLocation,
                        $reflectionAttributeLocatedOnClassProperty->newInstance(),
                    );
                }
            }

            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $classMethodLocation = AttributeCollector\Location\ClassMethodLocation::create(
                    $classLocation->className(),
                    AttributeCollector\Name\MethodName::fromString($reflectionMethod->getName()),
                );

                foreach ($reflectionMethod->getAttributes() as $reflectionAttributeLocatedOnMethod) {
                    $attributes[] = AttributeCollector\Attribute::create(
                        $classMethodLocation,
                        $reflectionAttributeLocatedOnMethod->newInstance(),
                    );
                }

                foreach ($reflectionMethod->getParameters() as $reflectionMethodParameter) {
                    $classMethodParameterLocation = AttributeCollector\Location\ClassMethodParameterLocation::create(
                        $classLocation->className(),
                        $classMethodLocation->methodName(),
                        AttributeCollector\Name\ParameterName::fromString($reflectionMethodParameter->getName()),
                    );

                    foreach ($reflectionMethodParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
                        $attributes[] = AttributeCollector\Attribute::create(
                            $classMethodParameterLocation,
                            $reflectionAttributeLocatedOnMethodParameter->newInstance(),
                        );
                    }
                }
            }

            $visitedLocationCollection->add($classLocation);
        }

        return AttributeCollector\AttributeCollection::create(...$attributes);
    }
}
