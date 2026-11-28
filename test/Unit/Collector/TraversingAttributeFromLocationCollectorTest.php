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

namespace Ergebnis\AttributeCollector\Test\Unit\Collector;

use Ergebnis\AttributeCollector;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Collector\TraversingAttributeFromLocationCollector
 *
 * @uses \Ergebnis\AttributeCollector\Attribute
 * @uses \Ergebnis\AttributeCollector\AttributeCollection
 * @uses \Ergebnis\AttributeCollector\Collector\VisitedLocationCollection
 * @uses \Ergebnis\AttributeCollector\Exception\AttributeCollectionNotSupported
 * @uses \Ergebnis\AttributeCollector\Exception\ClassConstantCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Exception\ClassCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Exception\ClassMethodCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Exception\ClassMethodDoesNotHaveParameter
 * @uses \Ergebnis\AttributeCollector\Exception\ClassPropertyCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Exception\ConstantCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Exception\FunctionCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Exception\FunctionDoesNotHaveParameter
 * @uses \Ergebnis\AttributeCollector\Location\ClassConstantLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodParameterLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassPropertyLocation
 * @uses \Ergebnis\AttributeCollector\Location\ConstantLocation
 * @uses \Ergebnis\AttributeCollector\Location\FunctionLocation
 * @uses \Ergebnis\AttributeCollector\Location\FunctionParameterLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 * @uses \Ergebnis\AttributeCollector\Name\FunctionName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 * @uses \Ergebnis\AttributeCollector\Name\ParameterName
 * @uses \Ergebnis\AttributeCollector\Name\PropertyName
 */
final class TraversingAttributeFromLocationCollectorTest extends Framework\TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../Fixture/ClassUsingAttributes.php';

        if (\PHP_VERSION_ID >= 80500) {
            require_once __DIR__ . '/../../Fixture/constants.php';
        }
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationsAreEmpty(): void
    {
        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation();

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationThrowsAttributeCollectionNotSupportedWhenLocationIsOfUnsupportedType(): void
    {
        $location = $this->createStub(AttributeCollector\Location\Location::class);

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\AttributeCollectionNotSupported::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassLocation(): void
    {
        $location = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString('UndefinedClass'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassLocationForClassNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassNotUsingAttributes::class));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassLocationForClassUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassLocationForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassLocationsForClassUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(\strtolower(AttributeCollector\Test\Fixture\ClassUsingAttributes::class))),
            AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
            AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(\strtoupper(AttributeCollector\Test\Fixture\ClassUsingAttributes::class))),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassLocationForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassConstantCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassConstantLocation(): void
    {
        $location = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString('UndefinedClass'),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassConstantCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassConstantCouldNotBeReflectedWhenClassConstantCouldNotBeReflectedForClassConstantLocation(): void
    {
        $location = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\ConstantName::fromString('BAR'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassConstantCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassConstantLocationForClassConstantNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassNotUsingAttributes::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassConstantLocationForClassConstantUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassConstantLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\ConstantName::fromString('FOO'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassConstantLocationForClassConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassConstantLocationsForClassConstantUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\ClassConstantLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtolower(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\ConstantName::fromString('FOO'),
            ),
            AttributeCollector\Location\ClassConstantLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\ConstantName::fromString('FOO'),
            ),
            AttributeCollector\Location\ClassConstantLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtoupper(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\ConstantName::fromString('FOO'),
            ),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassConstantLocationForClassConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassPropertyCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassPropertyLocation(): void
    {
        $location = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString('UndefinedClass'),
            AttributeCollector\Name\PropertyName::fromString('FOO'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassPropertyCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassPropertyCouldNotBeReflectedWhenClassPropertyCouldNotBeReflectedForClassPropertyLocation(): void
    {
        $location = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\PropertyName::fromString('bar'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassPropertyCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassPropertyLocationForClassPropertyNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassNotUsingAttributes::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassPropertyLocationForClassPropertyUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassPropertyLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\PropertyName::fromString('fooBar'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassPropertyLocationForClassPropertyUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassPropertyLocationsForClassPropertyUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\ClassPropertyLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtolower(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\PropertyName::fromString('fooBar'),
            ),
            AttributeCollector\Location\ClassPropertyLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\PropertyName::fromString('fooBar'),
            ),
            AttributeCollector\Location\ClassPropertyLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtoupper(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\PropertyName::fromString('fooBar'),
            ),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassPropertyLocationForClassPropertyUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassMethodLocation(): void
    {
        $location = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString('UndefinedClass'),
            AttributeCollector\Name\MethodName::fromString('bar'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassMethodCouldNotBeReflectedForClassMethodLocation(): void
    {
        $location = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('bar'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassMethodLocationForClassMethodNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassNotUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassMethodLocationForClassMethodUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassMethodLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassMethodLocationForClassMethodUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassMethodLocationsForClassMethodUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\ClassMethodLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtolower(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\MethodName::fromString(\strtolower('barBaz')),
            ),
            AttributeCollector\Location\ClassMethodLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\MethodName::fromString('barBaz'),
            ),
            AttributeCollector\Location\ClassMethodLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtoupper(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\MethodName::fromString(\strtoupper('barBaz')),
            ),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassMethodLocationForClassMethodUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassMethodParameterLocation(): void
    {
        $location = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString('UndefinedClass'),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
            AttributeCollector\Name\ParameterName::fromString('bazQux'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassMethodCouldNotBeReflectedForClassMethodParameterLocation(): void
    {
        $location = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('bar'),
            AttributeCollector\Name\ParameterName::fromString('bazQux'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassMethodDoesNotHaveParameterWhenClassMethodDoesNotHaveParameterForClassMethodParameterLocation(): void
    {
        $location = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
            AttributeCollector\Name\ParameterName::fromString('qux'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ClassMethodDoesNotHaveParameter::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassMethodParameterLocationForClassMethodParameterNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassNotUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
            AttributeCollector\Name\ParameterName::fromString('bazQux'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassMethodParameterLocationForClassMethodParameterUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ClassMethodParameterLocation::create(
            AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
            AttributeCollector\Name\MethodName::fromString('barBaz'),
            AttributeCollector\Name\ParameterName::fromString('bazQux'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassMethodParameterLocationForClassMethodParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassMethodParameterLocationsForClassMethodParameterUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\ClassMethodParameterLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtolower(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\MethodName::fromString(\strtolower('barBaz')),
                AttributeCollector\Name\ParameterName::fromString('bazQux'),
            ),
            AttributeCollector\Location\ClassMethodParameterLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\MethodName::fromString('barBaz'),
                AttributeCollector\Name\ParameterName::fromString('bazQux'),
            ),
            AttributeCollector\Location\ClassMethodParameterLocation::create(
                AttributeCollector\Name\ClassName::fromString(\strtoupper(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                AttributeCollector\Name\MethodName::fromString(\strtoupper('barBaz')),
                AttributeCollector\Name\ParameterName::fromString('bazQux'),
            ),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassMethodParameterLocationForClassMethodParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @requires PHP < 8.5.0
     */
    public function testCollectFromLocationThrowsAttributeCollectionNotSupportedWhenPhpVersionIsLessThanPhp85ForConstantLocation(): void
    {
        $location = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\AttributeCollectionNotSupported::class);

        $collector->collectFromLocation($location);
    }

    /**
     * @requires PHP >= 8.5.0
     */
    public function testCollectFromLocationThrowsConstantCouldNotBeReflectedWhenConstantCouldNotBeReflectedForConstantLocation(): void
    {
        $location = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('UndefinedNamespace\FOO'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\ConstantCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    /**
     * @requires PHP >= 8.5.0
     */
    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsConstantLocationForConstantNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\BAZ'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    /**
     * @requires PHP >= 8.5.0
     */
    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsConstantLocationForConstantUsingAttributes(): void
    {
        $location = AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromConstantLocationForConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @requires PHP >= 8.5.0
     */
    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateConstantLocationsForConstantUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString(\sprintf(
                '%s\FOO',
                \strtolower('Ergebnis\AttributeCollector\Test\Fixture'),
            ))),
            AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
            AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString(\sprintf(
                '%s\FOO',
                \strtoupper('Ergebnis\AttributeCollector\Test\Fixture'),
            ))),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromConstantLocationForConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsFunctionCouldNotBeReflectedWhenFunctionCouldNotBeReflectedForFunctionLocation(): void
    {
        $location = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('UndefinedNamespace\foo'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\FunctionCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsFunctionLocationForFunctionNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\plughXyzzy'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsFunctionLocationForFunctionUsingAttributes(): void
    {
        $location = AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'));

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromFunctionLocationForFunctionUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateFunctionLocationsForFunctionUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString(\strtolower('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'))),
            AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
            AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString(\strtoupper('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'))),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromFunctionLocationForFunctionUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsFunctionCouldNotBeReflectedWhenFunctionCouldNotBeReflectedForFunctionParameterLocation(): void
    {
        $location = AttributeCollector\Location\FunctionParameterLocation::create(
            AttributeCollector\Name\FunctionName::fromString('UndefinedNamespace\foo'),
            AttributeCollector\Name\ParameterName::fromString('barBaz'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\FunctionCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsFunctionDoesNotHaveParameterWhenFunctionDoesNotHaveParameterForFunctionParameterLocation(): void
    {
        $location = AttributeCollector\Location\FunctionParameterLocation::create(
            AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\garplyWaldo'),
            AttributeCollector\Name\ParameterName::fromString('quz'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(AttributeCollector\Exception\FunctionDoesNotHaveParameter::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsFunctionParameterLocationForFunctionParameterNotUsingAttributes(): void
    {
        $location = AttributeCollector\Location\FunctionParameterLocation::create(
            AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\plughXyzzy'),
            AttributeCollector\Name\ParameterName::fromString('xyzzyThud'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsFunctionParameterLocationForFunctionParameterUsingAttributes(): void
    {
        $location = AttributeCollector\Location\FunctionParameterLocation::create(
            AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
            AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
        );

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromFunctionParameterLocationForFunctionParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateFunctionParameterLocationsForFunctionParameterUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\FunctionParameterLocation::create(
                AttributeCollector\Name\FunctionName::fromString(\strtolower('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
            ),
            AttributeCollector\Location\FunctionParameterLocation::create(
                AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
            ),
            AttributeCollector\Location\FunctionParameterLocation::create(
                AttributeCollector\Name\FunctionName::fromString(\strtoupper('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
            ),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromFunctionParameterLocationForFunctionParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainOverlappingLocationsForClassUsingAttributesWhenClassMethodParameterWasAlreadyVisited(): void
    {
        $locations = [
            AttributeCollector\Location\ClassConstantLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\ConstantName::fromString('FOO'),
            ),
            AttributeCollector\Location\ClassPropertyLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\PropertyName::fromString('fooBar'),
            ),
            AttributeCollector\Location\ClassMethodParameterLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\MethodName::fromString('barBaz'),
                AttributeCollector\Name\ParameterName::fromString('bazQux'),
            ),
            AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainOverlappingLocationsForClassUsingAttributesWhenClassMethodWasAlreadyVisited(): void
    {
        $locations = [
            AttributeCollector\Location\ClassConstantLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\ConstantName::fromString('FOO'),
            ),
            AttributeCollector\Location\ClassPropertyLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\PropertyName::fromString('fooBar'),
            ),
            AttributeCollector\Location\ClassMethodLocation::create(
                AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                AttributeCollector\Name\MethodName::fromString('barBaz'),
            ),
            AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainOverlappingLocationsForFunctionsUsingAttributes(): void
    {
        $locations = [
            AttributeCollector\Location\FunctionParameterLocation::create(
                AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
            ),
            AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
        ];

        $collector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionParameterLocation::create(
                    AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'quz',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionParameterLocation::create(
                    AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'qux',
                    234,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromClassLocationForClassUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class)),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromClassConstantLocationForClassConstantUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassConstantLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\ConstantName::fromString('FOO'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromClassPropertyLocationForClassPropertyUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassPropertyLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\PropertyName::fromString('fooBar'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromClassMethodLocationForClassMethodUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromClassMethodParameterLocationForClassMethodParameterUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ClassMethodParameterLocation::create(
                    AttributeCollector\Name\ClassName::fromString(AttributeCollector\Test\Fixture\ClassUsingAttributes::class),
                    AttributeCollector\Name\MethodName::fromString('barBaz'),
                    AttributeCollector\Name\ParameterName::fromString('bazQux'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromConstantLocationForConstantUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromFunctionLocationForFunctionUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'qux',
                    234,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionParameterLocation::create(
                    AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'quz',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionParameterLocation::create(
                    AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromFunctionParameterLocationForFunctionParameterUsingAttributes(): array
    {
        return [
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionParameterLocation::create(
                    AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithParameters(
                    'quz',
                    345,
                ),
            ),
            AttributeCollector\Attribute::create(
                AttributeCollector\Location\FunctionParameterLocation::create(
                    AttributeCollector\Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    AttributeCollector\Name\ParameterName::fromString('corgeGrault'),
                ),
                new AttributeCollector\Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }
}
