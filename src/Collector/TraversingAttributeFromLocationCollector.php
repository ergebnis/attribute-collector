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

final class TraversingAttributeFromLocationCollector implements AttributeFromLocationCollector
{
    public function collectFromLocation(AttributeCollector\Location\Location ...$locations): AttributeCollector\AttributeCollection
    {
        $visitedLocationCollection = new VisitedLocationCollection();

        $attributes = [];

        foreach ($locations as $location) {
            $collectedAttributes = match ($location::class) {
                AttributeCollector\Location\ClassConstantLocation::class => self::collectFromClassConstantLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                AttributeCollector\Location\ClassLocation::class => self::collectFromClassLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                AttributeCollector\Location\ClassMethodLocation::class => self::collectFromClassMethodLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                AttributeCollector\Location\ClassMethodParameterLocation::class => self::collectFromClassMethodParameterLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                AttributeCollector\Location\ClassPropertyLocation::class => self::collectFromClassPropertyLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                AttributeCollector\Location\ConstantLocation::class => self::collectFromConstantLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                AttributeCollector\Location\FunctionLocation::class => self::collectFromFunctionLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                AttributeCollector\Location\FunctionParameterLocation::class => self::collectFromFunctionParameterLocation(
                    $location,
                    $visitedLocationCollection,
                ),
                default => throw AttributeCollector\Exception\AttributeCollectionNotSupported::forLocation($location),
            };

            if ([] === $collectedAttributes) {
                continue;
            }

            /** @var list<AttributeCollector\Attribute> $attributes */
            $attributes = [
                ...$attributes,
                ...$collectedAttributes,
            ];
        }

        return AttributeCollector\AttributeCollection::create(...$attributes);
    }

    /**
     * @throws AttributeCollector\Exception\ClassCouldNotBeReflected
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromClassLocation(
        AttributeCollector\Location\ClassLocation $classLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($classLocation)) {
            return [];
        }

        try {
            $reflectionClass = new \ReflectionClass($classLocation->className()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw AttributeCollector\Exception\ClassCouldNotBeReflected::forClassLocationWithReflectionException(
                $classLocation,
                $reflectionException,
            );
        }

        $attributes = [];

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

            if ($visitedLocationCollection->has($classConstantLocation)) {
                continue;
            }

            foreach ($reflectionConstant->getAttributes() as $reflectionAttributeLocatedOnClassConstant) {
                $attributes[] = AttributeCollector\Attribute::create(
                    $classConstantLocation,
                    $reflectionAttributeLocatedOnClassConstant->newInstance(),
                );
            }

            $visitedLocationCollection->add($classConstantLocation);
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $classPropertyLocation = AttributeCollector\Location\ClassPropertyLocation::create(
                $classLocation->className(),
                AttributeCollector\Name\PropertyName::fromString($reflectionProperty->getName()),
            );

            if ($visitedLocationCollection->has($classPropertyLocation)) {
                continue;
            }

            foreach ($reflectionProperty->getAttributes() as $reflectionAttributeLocatedOnClassProperty) {
                $attributes[] = AttributeCollector\Attribute::create(
                    $classPropertyLocation,
                    $reflectionAttributeLocatedOnClassProperty->newInstance(),
                );
            }

            $visitedLocationCollection->add($classPropertyLocation);
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $classMethodLocation = AttributeCollector\Location\ClassMethodLocation::create(
                $classLocation->className(),
                AttributeCollector\Name\MethodName::fromString($reflectionMethod->getName()),
            );

            if ($visitedLocationCollection->has($classMethodLocation)) {
                continue;
            }

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

                if ($visitedLocationCollection->has($classMethodParameterLocation)) {
                    continue;
                }

                foreach ($reflectionMethodParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
                    $attributes[] = AttributeCollector\Attribute::create(
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
     * @throws AttributeCollector\Exception\ClassConstantCouldNotBeReflected
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromClassConstantLocation(
        AttributeCollector\Location\ClassConstantLocation $classConstantLocation,
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
            throw AttributeCollector\Exception\ClassConstantCouldNotBeReflected::forClassConstantLocationAndReflectionException(
                $classConstantLocation,
                $reflectionException,
            );
        }

        $reflectionClass = new \ReflectionClass($classConstantLocation->className()->toString());

        $attributes = [];

        $classConstantLocation = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString($reflectionClass->getName()),
            AttributeCollector\Name\ConstantName::fromString($reflectionClassConstant->getName()),
        );

        foreach ($reflectionClassConstant->getAttributes() as $reflectionAttributeLocatedOnClassConstant) {
            $attributes[] = AttributeCollector\Attribute::create(
                $classConstantLocation,
                $reflectionAttributeLocatedOnClassConstant->newInstance(),
            );
        }

        $visitedLocationCollection->add($classConstantLocation);

        return $attributes;
    }

    /**
     * @throws AttributeCollector\Exception\ClassPropertyCouldNotBeReflected
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromClassPropertyLocation(
        AttributeCollector\Location\ClassPropertyLocation $classPropertyLocation,
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
            throw AttributeCollector\Exception\ClassPropertyCouldNotBeReflected::forClassPropertyLocationWithReflectionException(
                $classPropertyLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $reflectionClass = new \ReflectionClass($classPropertyLocation->className()->toString());

        $classPropertyLocation = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString($reflectionClass->getName()),
            AttributeCollector\Name\PropertyName::fromString($reflectionProperty->getName()),
        );

        foreach ($reflectionProperty->getAttributes() as $reflectionAttributeLocatedOnClassProperty) {
            $attributes[] = AttributeCollector\Attribute::create(
                $classPropertyLocation,
                $reflectionAttributeLocatedOnClassProperty->newInstance(),
            );
        }

        $visitedLocationCollection->add($classPropertyLocation);

        return $attributes;
    }

    /**
     * @throws AttributeCollector\Exception\ClassMethodCouldNotBeReflected
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromClassMethodLocation(
        AttributeCollector\Location\ClassMethodLocation $classMethodLocation,
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
            throw AttributeCollector\Exception\ClassMethodCouldNotBeReflected::forClassMethodLocationWithReflectionException(
                $classMethodLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $reflectionClass = new \ReflectionClass($classMethodLocation->className()->toString());

        $classMethodLocation = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString($reflectionClass->getName()),
            AttributeCollector\Name\MethodName::fromString($reflectionMethod->getName()),
        );

        foreach ($reflectionMethod->getAttributes() as $reflectionAttributeLocatedOnClassMethod) {
            $attributes[] = AttributeCollector\Attribute::create(
                $classMethodLocation,
                $reflectionAttributeLocatedOnClassMethod->newInstance(),
            );

            foreach ($reflectionMethod->getParameters() as $reflectionMethodParameter) {
                $classMethodParameterLocation = AttributeCollector\Location\ClassMethodParameterLocation::create(
                    $classMethodLocation->className(),
                    $classMethodLocation->methodName(),
                    AttributeCollector\Name\ParameterName::fromString($reflectionMethodParameter->getName()),
                );

                if ($visitedLocationCollection->has($classMethodParameterLocation)) {
                    continue;
                }

                foreach ($reflectionMethodParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
                    $attributes[] = AttributeCollector\Attribute::create(
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
     * @throws AttributeCollector\Exception\ClassMethodCouldNotBeReflected
     * @throws AttributeCollector\Exception\ClassMethodDoesNotHaveParameter
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromClassMethodParameterLocation(
        AttributeCollector\Location\ClassMethodParameterLocation $classMethodParameterLocation,
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
            throw AttributeCollector\Exception\ClassMethodCouldNotBeReflected::forClassMethodLocationWithReflectionException(
                AttributeCollector\Location\ClassMethodLocation::create(
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
            throw AttributeCollector\Exception\ClassMethodDoesNotHaveParameter::forClassMethodParameterLocation($classMethodParameterLocation);
        }

        $attributes = [];

        $reflectionClass = new \ReflectionClass($classMethodParameterLocation->className()->toString());

        $classMethodParameterLocation = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString($reflectionClass->getName()),
            AttributeCollector\Name\MethodName::fromString($reflectionMethod->getName()),
            AttributeCollector\Name\ParameterName::fromString($reflectionParameter->getName()),
        );

        foreach ($reflectionParameter->getAttributes() as $reflectionAttributeLocatedOnMethodParameter) {
            $attributes[] = AttributeCollector\Attribute::create(
                $classMethodParameterLocation,
                $reflectionAttributeLocatedOnMethodParameter->newInstance(),
            );
        }

        $visitedLocationCollection->add($classMethodParameterLocation);

        return $attributes;
    }

    private static function findReflectionMethodParameterWithParameterName(
        \ReflectionMethod $reflectionMethod,
        AttributeCollector\Name\ParameterName $parameterName,
    ): ?\ReflectionParameter {
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if ($reflectionParameter->getName() === $parameterName->toString()) {
                return $reflectionParameter;
            }
        }

        return null;
    }

    /**
     * @throws AttributeCollector\Exception\AttributeCollectionNotSupported
     * @throws AttributeCollector\Exception\ConstantCouldNotBeReflected
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromConstantLocation(
        AttributeCollector\Location\ConstantLocation $constantLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if (\PHP_VERSION_ID < 80500) {
            throw AttributeCollector\Exception\AttributeCollectionNotSupported::forLocation($constantLocation);
        }

        if ($visitedLocationCollection->has($constantLocation)) {
            return [];
        }

        try {
            $reflectionConstant = new \ReflectionConstant($constantLocation->constantName()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw AttributeCollector\Exception\ConstantCouldNotBeReflected::forConstantLocationWithReflectionException(
                $constantLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $constantLocation = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString($reflectionConstant->getName()));

        foreach ($reflectionConstant->getAttributes() as $reflectionAttributedLocatedOnConstant) {
            $attributes[] = AttributeCollector\Attribute::create(
                $constantLocation,
                $reflectionAttributedLocatedOnConstant->newInstance(),
            );
        }

        $visitedLocationCollection->add($constantLocation);

        return $attributes;
    }

    /**
     * @throws AttributeCollector\Exception\FunctionCouldNotBeReflected
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromFunctionLocation(
        AttributeCollector\Location\FunctionLocation $functionLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($functionLocation)) {
            return [];
        }

        try {
            $reflectionFunction = new \ReflectionFunction($functionLocation->functionName()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw AttributeCollector\Exception\FunctionCouldNotBeReflected::forFunctionLocationWithReflectionException(
                $functionLocation,
                $reflectionException,
            );
        }

        $attributes = [];

        $functionLocation = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString($reflectionFunction->getName()));

        foreach ($reflectionFunction->getAttributes() as $reflectionAttributeLocatedOnFunction) {
            $attributes[] = AttributeCollector\Attribute::create(
                $functionLocation,
                $reflectionAttributeLocatedOnFunction->newInstance(),
            );

            foreach ($reflectionFunction->getParameters() as $reflectionFunctionParameter) {
                $functionParameterLocation = AttributeCollector\Location\FunctionParameterLocation::create(
                    $functionLocation->functionName(),
                    AttributeCollector\Name\ParameterName::fromString($reflectionFunctionParameter->getName()),
                );

                if ($visitedLocationCollection->has($functionParameterLocation)) {
                    continue;
                }

                foreach ($reflectionFunctionParameter->getAttributes() as $reflectionAttributeLocatedOnFunctionParameter) {
                    $attributes[] = AttributeCollector\Attribute::create(
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
     * @throws AttributeCollector\Exception\FunctionCouldNotBeReflected
     *
     * @return list<AttributeCollector\Attribute>
     */
    private static function collectFromFunctionParameterLocation(
        AttributeCollector\Location\FunctionParameterLocation $functionParameterLocation,
        VisitedLocationCollection $visitedLocationCollection,
    ): array {
        if ($visitedLocationCollection->has($functionParameterLocation)) {
            return [];
        }

        try {
            $reflectionFunction = new \ReflectionFunction($functionParameterLocation->functionName()->toString());
        } catch (\ReflectionException $reflectionException) {
            throw AttributeCollector\Exception\FunctionCouldNotBeReflected::forFunctionLocationWithReflectionException(
                AttributeCollector\Location\FunctionLocation::create($functionParameterLocation->functionName()),
                $reflectionException,
            );
        }

        $reflectionParameter = self::findReflectionFunctionParameterWithParameterName(
            $reflectionFunction,
            $functionParameterLocation->parameterName(),
        );

        if (!$reflectionParameter instanceof \ReflectionParameter) {
            throw AttributeCollector\Exception\FunctionDoesNotHaveParameter::forFunctionParameterLocation($functionParameterLocation);
        }

        $attributes = [];

        $functionParameterLocation = AttributeCollector\Location\FunctionParameterLocation::create(
            AttributeCollector\Name\FunctionName::fromString($reflectionFunction->getName()),
            AttributeCollector\Name\ParameterName::fromString($reflectionParameter->getName()),
        );

        foreach ($reflectionParameter->getAttributes() as $reflectionAttributeLocatedOnFunctionParameter) {
            $attributes[] = AttributeCollector\Attribute::create(
                $functionParameterLocation,
                $reflectionAttributeLocatedOnFunctionParameter->newInstance(),
            );
        }

        $visitedLocationCollection->add($functionParameterLocation);

        return $attributes;
    }

    private static function findReflectionFunctionParameterWithParameterName(
        \ReflectionFunction $reflectionFunction,
        AttributeCollector\Name\ParameterName $parameterName,
    ): ?\ReflectionParameter {
        foreach ($reflectionFunction->getParameters() as $reflectionParameter) {
            if ($reflectionParameter->getName() === $parameterName->toString()) {
                return $reflectionParameter;
            }
        }

        return null;
    }
}
