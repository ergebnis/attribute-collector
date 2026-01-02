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

final class TraversingAttributeFromLocationCollector implements AttributeFromLocationCollector
{
    public function collectFromLocation(Location\Location ...$locations): AttributeCollection
    {
        $visitedLocationCollection = new VisitedLocationCollection();

        $attributes = [];

        foreach ($locations as $location) {
            $collectedAttributes = match ($location::class) {
                Location\ClassLocation::class => self::collectFromClassLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                Location\ClassConstantLocation::class => self::collectFromClassConstantLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                Location\ClassPropertyLocation::class => self::collectFromClassPropertyLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                Location\ClassMethodLocation::class => self::collectFromClassMethodLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                Location\ClassMethodParameterLocation::class => self::collectFromClassMethodParameterLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                Location\ConstantLocation::class => self::collectFromConstantLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                Location\FunctionLocation::class => self::collectFromFunctionLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                Location\FunctionParameterLocation::class => self::collectFromFunctionParameterLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                default => throw Exception\AttributeCollectionNotSupported::forLocation($location),
            };

            if ([] === $collectedAttributes) {
                continue;
            }

            /** @var list<Attribute> $attributes */
            $attributes = [
                ...$attributes,
                ...$collectedAttributes,
            ];
        }

        return AttributeCollection::create(...$attributes);
    }

    /**
     * @throws Exception\ClassCouldNotBeReflected
     *
     * @return list<Attribute>
     */
    private static function collectFromClassLocation(
        Location\ClassLocation $classLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($classLocation)) {
            return [];
        }

        try {
            $reflectionClass = new \ReflectionClass($classLocation->className()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw Exception\ClassCouldNotBeReflected::forClassLocationWithReflectionException(
                $classLocation,
                $reflectionException,
            );
        }

        $attributes = [];

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

            if ($visitedLocationCollection->has($classConstantLocation)) {
                continue;
            }

            foreach ($reflectionConstant->getAttributes() as $reflectionAttributeLocatedOnClassConstant) {
                $attributes[] = Attribute::create(
                    $classConstantLocation,
                    $reflectionAttributeLocatedOnClassConstant->newInstance(),
                );
            }

            $visitedLocationCollection->add($classConstantLocation);
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $classPropertyLocation = Location\ClassPropertyLocation::create(
                $classLocation->className(),
                Name\PropertyName::fromString($reflectionProperty->getName()),
            );

            if ($visitedLocationCollection->has($classPropertyLocation)) {
                continue;
            }

            foreach ($reflectionProperty->getAttributes() as $reflectionAttributeLocatedOnClassProperty) {
                $attributes[] = Attribute::create(
                    $classPropertyLocation,
                    $reflectionAttributeLocatedOnClassProperty->newInstance(),
                );
            }

            $visitedLocationCollection->add($classPropertyLocation);
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $classMethodLocation = Location\ClassMethodLocation::create(
                $classLocation->className(),
                Name\MethodName::fromString($reflectionMethod->getName()),
            );

            if ($visitedLocationCollection->has($classMethodLocation)) {
                continue;
            }

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

                if ($visitedLocationCollection->has($classMethodParameterLocation)) {
                    continue;
                }

                foreach ($reflectionMethodParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
                    $attributes[] = Attribute::create(
                        $classMethodParameterLocation,
                        $reflectionAttributeLocatedOnMethodParameter->newInstance(),
                    );
                }

                $visitedLocationCollection->add($classMethodParameterLocation);
            }

            $visitedLocationCollection->add($classMethodLocation);
        }

        $visitedLocationCollection->add($classLocation);

        return $attributes;
    }

    /**
     * @throws Exception\ClassConstantCouldNotBeReflected
     *
     * @return list<Attribute>
     */
    private static function collectFromClassConstantLocation(
        Location\ClassConstantLocation $classConstantLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($classConstantLocation)) {
            return [];
        }

        try {
            $reflectionClassConstant = new \ReflectionClassConstant(
                $classConstantLocation->className()->toString(),
                $classConstantLocation->constantName()->toString(),
            );
        } catch (\ReflectionException $reflectionException) {
            throw Exception\ClassConstantCouldNotBeReflected::forClassConstantLocationAndReflectionException(
                $classConstantLocation,
                $reflectionException,
            );
        }

        $reflectionClass = new \ReflectionClass($classConstantLocation->className()->toString());

        $attributes = [];

        $classConstantLocation = Location\ClassConstantLocation::create(
            Name\ClassName::fromString($reflectionClass->getName()),
            Name\ConstantName::fromString($reflectionClassConstant->getName()),
        );

        foreach ($reflectionClassConstant->getAttributes() as $reflectionAttributeLocatedOnClassConstant) {
            $attributes[] = Attribute::create(
                $classConstantLocation,
                $reflectionAttributeLocatedOnClassConstant->newInstance(),
            );
        }

        $visitedLocationCollection->add($classConstantLocation);

        return $attributes;
    }

    /**
     * @throws Exception\ClassPropertyCouldNotBeReflected
     *
     * @return list<Attribute>
     */
    private static function collectFromClassPropertyLocation(
        Location\ClassPropertyLocation $classPropertyLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($classPropertyLocation)) {
            return [];
        }

        try {
            $reflectionProperty = new \ReflectionProperty(
                $classPropertyLocation->className()->toString(),
                $classPropertyLocation->propertyName()->toString(),
            );
        } catch (\ReflectionException $reflectionException) {
            throw Exception\ClassPropertyCouldNotBeReflected::forClassPropertyLocationWithReflectionException(
                $classPropertyLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $reflectionClass = new \ReflectionClass($classPropertyLocation->className()->toString());

        $classPropertyLocation = Location\ClassPropertyLocation::create(
            Name\ClassName::fromString($reflectionClass->getName()),
            Name\PropertyName::fromString($reflectionProperty->getName()),
        );

        foreach ($reflectionProperty->getAttributes() as $reflectionAttributeLocatedOnClassProperty) {
            $attributes[] = Attribute::create(
                $classPropertyLocation,
                $reflectionAttributeLocatedOnClassProperty->newInstance(),
            );
        }

        $visitedLocationCollection->add($classPropertyLocation);

        return $attributes;
    }

    /**
     * @throws Exception\ClassMethodCouldNotBeReflected
     *
     * @return list<Attribute>
     */
    private static function collectFromClassMethodLocation(
        Location\ClassMethodLocation $classMethodLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($classMethodLocation)) {
            return [];
        }

        try {
            $reflectionMethod = new \ReflectionMethod(
                $classMethodLocation->className()->toString(),
                $classMethodLocation->methodName()->toString(),
            );
        } catch (\ReflectionException $reflectionException) {
            throw Exception\ClassMethodCouldNotBeReflected::forClassMethodLocationWithReflectionException(
                $classMethodLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $reflectionClass = new \ReflectionClass($classMethodLocation->className()->toString());

        $classMethodLocation = Location\ClassMethodLocation::create(
            Name\ClassName::fromString($reflectionClass->getName()),
            Name\MethodName::fromString($reflectionMethod->getName()),
        );

        foreach ($reflectionMethod->getAttributes() as $reflectionAttributeLocatedOnClassMethod) {
            $attributes[] = Attribute::create(
                $classMethodLocation,
                $reflectionAttributeLocatedOnClassMethod->newInstance(),
            );

            foreach ($reflectionMethod->getParameters() as $reflectionMethodParameter) {
                $classMethodParameterLocation = Location\ClassMethodParameterLocation::create(
                    $classMethodLocation->className(),
                    $classMethodLocation->methodName(),
                    Name\ParameterName::fromString($reflectionMethodParameter->getName()),
                );

                if ($visitedLocationCollection->has($classMethodParameterLocation)) {
                    continue;
                }

                foreach ($reflectionMethodParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
                    $attributes[] = Attribute::create(
                        $classMethodParameterLocation,
                        $reflectionAttributeLocatedOnMethodParameter->newInstance(),
                    );
                }

                $visitedLocationCollection->add($classMethodParameterLocation);
            }

            $visitedLocationCollection->add($classMethodLocation);
        }

        return $attributes;
    }

    /**
     * @throws Exception\ClassMethodCouldNotBeReflected
     * @throws Exception\ClassMethodDoesNotHaveParameter
     *
     * @return list<Attribute>
     */
    private static function collectFromClassMethodParameterLocation(
        Location\ClassMethodParameterLocation $classMethodParameterLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($classMethodParameterLocation)) {
            return [];
        }

        try {
            $reflectionMethod = new \ReflectionMethod(
                $classMethodParameterLocation->className()->toString(),
                $classMethodParameterLocation->methodName()->toString(),
            );
        } catch (\ReflectionException $reflectionException) {
            throw Exception\ClassMethodCouldNotBeReflected::forClassMethodLocationWithReflectionException(
                Location\ClassMethodLocation::create(
                    $classMethodParameterLocation->className(),
                    $classMethodParameterLocation->methodName(),
                ),
                $reflectionException,
            );
        }

        $reflectionParameter = self::findReflectionMethodParameterWithParameterName(
            $reflectionMethod,
            $classMethodParameterLocation->parameterName(),
        );

        if (!$reflectionParameter instanceof \ReflectionParameter) {
            throw Exception\ClassMethodDoesNotHaveParameter::forClassMethodParameterLocation($classMethodParameterLocation);
        }

        $attributes = [];

        $reflectionClass = new \ReflectionClass($classMethodParameterLocation->className()->toString());

        $classMethodParameterLocation = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString($reflectionClass->getName()),
            Name\MethodName::fromString($reflectionMethod->getName()),
            Name\ParameterName::fromString($reflectionParameter->getName()),
        );

        foreach ($reflectionParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
            $attributes[] = Attribute::create(
                $classMethodParameterLocation,
                $reflectionAttributeLocatedOnMethodParameter->newInstance(),
            );
        }

        $visitedLocationCollection->add($classMethodParameterLocation);

        return $attributes;
    }

    private static function findReflectionMethodParameterWithParameterName(
        \ReflectionMethod $reflectionMethod,
        Name\ParameterName $parameterName,
    ): ?\ReflectionParameter {
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if ($reflectionParameter->getName() === $parameterName->toString()) {
                return $reflectionParameter;
            }
        }

        return null;
    }

    /**
     * @throws Exception\AttributeCollectionNotSupported
     * @throws Exception\ConstantCouldNotBeReflected
     *
     * @return list<Attribute>
     */
    private static function collectFromConstantLocation(
        Location\ConstantLocation $constantLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if (\PHP_VERSION_ID < 80500) {
            throw Exception\AttributeCollectionNotSupported::forLocation($constantLocation);
        }

        if ($visitedLocationCollection->has($constantLocation)) {
            return [];
        }

        try {
            $reflectionConstant = new \ReflectionConstant($constantLocation->constantName()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw Exception\ConstantCouldNotBeReflected::forConstantLocationWithReflectionException(
                $constantLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $constantLocation = Location\ConstantLocation::create(Name\ConstantName::fromString($reflectionConstant->getName()));

        foreach ($reflectionConstant->getAttributes() as $reflectionAttributedLocatedOnConstant) {
            $attributes[] = Attribute::create(
                $constantLocation,
                $reflectionAttributedLocatedOnConstant->newInstance(),
            );
        }

        $visitedLocationCollection->add($constantLocation);

        return $attributes;
    }

    /**
     * @throws Exception\FunctionCouldNotBeReflected
     *
     * @return list<Attribute>
     */
    private static function collectFromFunctionLocation(
        Location\FunctionLocation $functionLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($functionLocation)) {
            return [];
        }

        try {
            $reflectionFunction = new \ReflectionFunction($functionLocation->functionName()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw Exception\FunctionCouldNotBeReflected::forFunctionLocationWithReflectionException(
                $functionLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $functionLocation = Location\FunctionLocation::create(Name\FunctionName::fromString($reflectionFunction->getName()));

        foreach ($reflectionFunction->getAttributes() as $reflectionAttributeLocatedOnFunction) {
            $attributes[] = Attribute::create(
                $functionLocation,
                $reflectionAttributeLocatedOnFunction->newInstance(),
            );

            foreach ($reflectionFunction->getParameters() as $reflectionFunctionParameter) {
                $functionParameterLocation = Location\FunctionParameterLocation::create(
                    $functionLocation->functionName(),
                    Name\ParameterName::fromString($reflectionFunctionParameter->getName()),
                );

                if ($visitedLocationCollection->has($functionParameterLocation)) {
                    continue;
                }

                foreach ($reflectionFunctionParameter->getAttributes() as $reflectionAttributeLocatedOnFunctionParameter) {
                    $attributes[] = Attribute::create(
                        $functionParameterLocation,
                        $reflectionAttributeLocatedOnFunctionParameter->newInstance(),
                    );
                }

                $visitedLocationCollection->add($functionParameterLocation);
            }
        }

        $visitedLocationCollection->add($functionLocation);

        return $attributes;
    }

    /**
     * @throws Exception\FunctionCouldNotBeReflected
     *
     * @return list<Attribute>
     */
    private static function collectFromFunctionParameterLocation(
        Location\FunctionParameterLocation $functionParameterLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($functionParameterLocation)) {
            return [];
        }

        try {
            $reflectionFunction = new \ReflectionFunction($functionParameterLocation->functionName()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw Exception\FunctionCouldNotBeReflected::forFunctionLocationWithReflectionException(
                Location\FunctionLocation::create($functionParameterLocation->functionName()),
                $reflectionException,
            );
        }

        $reflectionParameter = self::findReflectionFunctionParameterWithParameterName(
            $reflectionFunction,
            $functionParameterLocation->parameterName(),
        );

        if (!$reflectionParameter instanceof \ReflectionParameter) {
            throw Exception\FunctionDoesNotHaveParameter::forFunctionParameterLocation($functionParameterLocation);
        }

        $attributes = [];

        $functionParameterLocation = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString($reflectionFunction->getName()),
            Name\ParameterName::fromString($reflectionParameter->getName()),
        );

        foreach ($reflectionParameter->getAttributes() as $reflectionAttributeLocatedOnFunctionParameter) {
            $attributes[] = Attribute::create(
                $functionParameterLocation,
                $reflectionAttributeLocatedOnFunctionParameter->newInstance(),
            );
        }

        $visitedLocationCollection->add($functionParameterLocation);

        return $attributes;
    }

    private static function findReflectionFunctionParameterWithParameterName(
        \ReflectionFunction $reflectionFunction,
        Name\ParameterName $parameterName,
    ): ?\ReflectionParameter {
        foreach ($reflectionFunction->getParameters() as $reflectionParameter) {
            if ($reflectionParameter->getName() === $parameterName->toString()) {
                return $reflectionParameter;
            }
        }

        return null;
    }
}
