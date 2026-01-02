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

use Ergebnis\AttributeCollector\Attribute;
use Ergebnis\AttributeCollector\Collector;
use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
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
        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation();

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationThrowsAttributeCollectionNotSupportedWhenLocationIsOfUnsupportedType(): void
    {
        $location = $this->createStub(Location\Location::class);

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\AttributeCollectionNotSupported::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassLocation(): void
    {
        $location = Location\ClassLocation::create(Name\ClassName::fromString('UndefinedClass'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassLocationForClassNotUsingAttributes(): void
    {
        $location = Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassNotUsingAttributes::class));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassLocationForClassUsingAttributes(): void
    {
        $location = Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassLocationForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassLocationsForClassUsingAttributes(): void
    {
        $locations = [
            Location\ClassLocation::create(Name\ClassName::fromString(\strtolower(Test\Fixture\ClassUsingAttributes::class))),
            Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
            Location\ClassLocation::create(Name\ClassName::fromString(\strtoupper(Test\Fixture\ClassUsingAttributes::class))),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassLocationForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassConstantCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassConstantLocation(): void
    {
        $location = Location\ClassConstantLocation::create(
            Name\ClassName::fromString('UndefinedClass'),
            Name\ConstantName::fromString('FOO'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassConstantCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassConstantCouldNotBeReflectedWhenClassConstantCouldNotBeReflectedForClassConstantLocation(): void
    {
        $location = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\ConstantName::fromString('BAR'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassConstantCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassConstantLocationForClassConstantNotUsingAttributes(): void
    {
        $location = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassNotUsingAttributes::class),
            Name\ConstantName::fromString('FOO'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassConstantLocationForClassConstantUsingAttributes(): void
    {
        $location = Location\ClassConstantLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\ConstantName::fromString('FOO'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassConstantLocationForClassConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassConstantLocationsForClassConstantUsingAttributes(): void
    {
        $locations = [
            Location\ClassConstantLocation::create(
                Name\ClassName::fromString(\strtolower(Test\Fixture\ClassUsingAttributes::class)),
                Name\ConstantName::fromString('FOO'),
            ),
            Location\ClassConstantLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\ConstantName::fromString('FOO'),
            ),
            Location\ClassConstantLocation::create(
                Name\ClassName::fromString(\strtoupper(Test\Fixture\ClassUsingAttributes::class)),
                Name\ConstantName::fromString('FOO'),
            ),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassConstantLocationForClassConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassPropertyCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassPropertyLocation(): void
    {
        $location = Location\ClassPropertyLocation::create(
            Name\ClassName::fromString('UndefinedClass'),
            Name\PropertyName::fromString('FOO'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassPropertyCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassPropertyCouldNotBeReflectedWhenClassPropertyCouldNotBeReflectedForClassPropertyLocation(): void
    {
        $location = Location\ClassPropertyLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\PropertyName::fromString('bar'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassPropertyCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassPropertyLocationForClassPropertyNotUsingAttributes(): void
    {
        $location = Location\ClassPropertyLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassNotUsingAttributes::class),
            Name\PropertyName::fromString('fooBar'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassPropertyLocationForClassPropertyUsingAttributes(): void
    {
        $location = Location\ClassPropertyLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\PropertyName::fromString('fooBar'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassPropertyLocationForClassPropertyUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassPropertyLocationsForClassPropertyUsingAttributes(): void
    {
        $locations = [
            Location\ClassPropertyLocation::create(
                Name\ClassName::fromString(\strtolower(Test\Fixture\ClassUsingAttributes::class)),
                Name\PropertyName::fromString('fooBar'),
            ),
            Location\ClassPropertyLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\PropertyName::fromString('fooBar'),
            ),
            Location\ClassPropertyLocation::create(
                Name\ClassName::fromString(\strtoupper(Test\Fixture\ClassUsingAttributes::class)),
                Name\PropertyName::fromString('fooBar'),
            ),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassPropertyLocationForClassPropertyUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassMethodLocation(): void
    {
        $location = Location\ClassMethodLocation::create(
            Name\ClassName::fromString('UndefinedClass'),
            Name\MethodName::fromString('bar'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassMethodCouldNotBeReflectedForClassMethodLocation(): void
    {
        $location = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\MethodName::fromString('bar'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassMethodLocationForClassMethodNotUsingAttributes(): void
    {
        $location = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassNotUsingAttributes::class),
            Name\MethodName::fromString('barBaz'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassMethodLocationForClassMethodUsingAttributes(): void
    {
        $location = Location\ClassMethodLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\MethodName::fromString('barBaz'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassMethodLocationForClassMethodUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassMethodLocationsForClassMethodUsingAttributes(): void
    {
        $locations = [
            Location\ClassMethodLocation::create(
                Name\ClassName::fromString(\strtolower(Test\Fixture\ClassUsingAttributes::class)),
                Name\MethodName::fromString(\strtolower('barBaz')),
            ),
            Location\ClassMethodLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\MethodName::fromString('barBaz'),
            ),
            Location\ClassMethodLocation::create(
                Name\ClassName::fromString(\strtoupper(Test\Fixture\ClassUsingAttributes::class)),
                Name\MethodName::fromString(\strtoupper('barBaz')),
            ),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassMethodLocationForClassMethodUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassCouldNotBeReflectedForClassMethodParameterLocation(): void
    {
        $location = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString('UndefinedClass'),
            Name\MethodName::fromString('barBaz'),
            Name\ParameterName::fromString('bazQux'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassMethodCouldNotBeReflectedWhenClassMethodCouldNotBeReflectedForClassMethodParameterLocation(): void
    {
        $location = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\MethodName::fromString('bar'),
            Name\ParameterName::fromString('bazQux'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassMethodCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsClassMethodDoesNotHaveParameterWhenClassMethodDoesNotHaveParameterForClassMethodParameterLocation(): void
    {
        $location = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\MethodName::fromString('barBaz'),
            Name\ParameterName::fromString('qux'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ClassMethodDoesNotHaveParameter::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsClassMethodParameterLocationForClassMethodParameterNotUsingAttributes(): void
    {
        $location = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassNotUsingAttributes::class),
            Name\MethodName::fromString('barBaz'),
            Name\ParameterName::fromString('bazQux'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsClassMethodParameterLocationForClassMethodParameterUsingAttributes(): void
    {
        $location = Location\ClassMethodParameterLocation::create(
            Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
            Name\MethodName::fromString('barBaz'),
            Name\ParameterName::fromString('bazQux'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromClassMethodParameterLocationForClassMethodParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateClassMethodParameterLocationsForClassMethodParameterUsingAttributes(): void
    {
        $locations = [
            Location\ClassMethodParameterLocation::create(
                Name\ClassName::fromString(\strtolower(Test\Fixture\ClassUsingAttributes::class)),
                Name\MethodName::fromString(\strtolower('barBaz')),
                Name\ParameterName::fromString('bazQux'),
            ),
            Location\ClassMethodParameterLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\MethodName::fromString('barBaz'),
                Name\ParameterName::fromString('bazQux'),
            ),
            Location\ClassMethodParameterLocation::create(
                Name\ClassName::fromString(\strtoupper(Test\Fixture\ClassUsingAttributes::class)),
                Name\MethodName::fromString(\strtoupper('barBaz')),
                Name\ParameterName::fromString('bazQux'),
            ),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromClassMethodParameterLocationForClassMethodParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @requires PHP < 8.5
     */
    public function testCollectFromLocationThrowsAttributeCollectionNotSupportedWhenPhpVersionIsLessThanPhp85ForConstantLocation(): void
    {
        $location = Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\AttributeCollectionNotSupported::class);

        $collector->collectFromLocation($location);
    }

    /**
     * @requires PHP >= 8.5
     */
    public function testCollectFromLocationThrowsConstantCouldNotBeReflectedWhenConstantCouldNotBeReflectedForConstantLocation(): void
    {
        $location = Location\ConstantLocation::create(Name\ConstantName::fromString('UndefinedNamespace\FOO'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\ConstantCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    /**
     * @requires PHP >= 8.5
     */
    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsConstantLocationForConstantNotUsingAttributes(): void
    {
        $location = Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\BAZ'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    /**
     * @requires PHP >= 8.5
     */
    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsConstantLocationForConstantUsingAttributes(): void
    {
        $location = Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromConstantLocationForConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @requires PHP >= 8.5
     */
    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateConstantLocationsForConstantUsingAttributes(): void
    {
        $locations = [
            Location\ConstantLocation::create(Name\ConstantName::fromString(\sprintf(
                '%s\FOO',
                \strtolower('Ergebnis\AttributeCollector\Test\Fixture'),
            ))),
            Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
            Location\ConstantLocation::create(Name\ConstantName::fromString(\sprintf(
                '%s\FOO',
                \strtoupper('Ergebnis\AttributeCollector\Test\Fixture'),
            ))),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromConstantLocationForConstantUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsFunctionCouldNotBeReflectedWhenFunctionCouldNotBeReflectedForFunctionLocation(): void
    {
        $location = Location\FunctionLocation::create(Name\FunctionName::fromString('UndefinedNamespace\foo'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\FunctionCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsFunctionLocationForFunctionNotUsingAttributes(): void
    {
        $location = Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\plughXyzzy'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsFunctionLocationForFunctionUsingAttributes(): void
    {
        $location = Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'));

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromFunctionLocationForFunctionUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateFunctionLocationsForFunctionUsingAttributes(): void
    {
        $locations = [
            Location\FunctionLocation::create(Name\FunctionName::fromString(\strtolower('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'))),
            Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
            Location\FunctionLocation::create(Name\FunctionName::fromString(\strtoupper('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'))),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromFunctionLocationForFunctionUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationThrowsFunctionCouldNotBeReflectedWhenFunctionCouldNotBeReflectedForFunctionParameterLocation(): void
    {
        $location = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('UndefinedNamespace\foo'),
            Name\ParameterName::fromString('barBaz'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\FunctionCouldNotBeReflected::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationThrowsFunctionDoesNotHaveParameterWhenFunctionDoesNotHaveParameterForFunctionParameterLocation(): void
    {
        $location = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\garplyWaldo'),
            Name\ParameterName::fromString('quz'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $this->expectException(Exception\FunctionDoesNotHaveParameter::class);

        $collector->collectFromLocation($location);
    }

    public function testCollectFromLocationReturnsEmptyAttributeCollectionWhenLocationIsFunctionParameterLocationForFunctionParameterNotUsingAttributes(): void
    {
        $location = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\plughXyzzy'),
            Name\ParameterName::fromString('xyzzyThud'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationIsFunctionParameterLocationForFunctionParameterUsingAttributes(): void
    {
        $location = Location\FunctionParameterLocation::create(
            Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
            Name\ParameterName::fromString('corgeGrault'),
        );

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation($location);

        $expected = self::attributesFromFunctionParameterLocationForFunctionParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainDuplicateFunctionParameterLocationsForFunctionParameterUsingAttributes(): void
    {
        $locations = [
            Location\FunctionParameterLocation::create(
                Name\FunctionName::fromString(\strtolower('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                Name\ParameterName::fromString('corgeGrault'),
            ),
            Location\FunctionParameterLocation::create(
                Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                Name\ParameterName::fromString('corgeGrault'),
            ),
            Location\FunctionParameterLocation::create(
                Name\FunctionName::fromString(\strtoupper('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                Name\ParameterName::fromString('corgeGrault'),
            ),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = self::attributesFromFunctionParameterLocationForFunctionParameterUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainOverlappingLocationsForClassUsingAttributesWhenClassMethodParameterWasAlreadyVisited(): void
    {
        $locations = [
            Location\ClassConstantLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\ConstantName::fromString('FOO'),
            ),
            Location\ClassPropertyLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\PropertyName::fromString('fooBar'),
            ),
            Location\ClassMethodParameterLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\MethodName::fromString('barBaz'),
                Name\ParameterName::fromString('bazQux'),
            ),
            Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = [
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainOverlappingLocationsForClassUsingAttributesWhenClassMethodWasAlreadyVisited(): void
    {
        $locations = [
            Location\ClassConstantLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\ConstantName::fromString('FOO'),
            ),
            Location\ClassPropertyLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\PropertyName::fromString('fooBar'),
            ),
            Location\ClassMethodLocation::create(
                Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                Name\MethodName::fromString('barBaz'),
            ),
            Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = [
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromLocationReturnsAttributeCollectionWhenLocationsContainOverlappingLocationsForFunctionsUsingAttributes(): void
    {
        $locations = [
            Location\FunctionParameterLocation::create(
                Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                Name\ParameterName::fromString('corgeGrault'),
            ),
            Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
        ];

        $collector = new Collector\TraversingAttributeFromLocationCollector();

        $collection = $collector->collectFromLocation(...$locations);

        $expected = [
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'quz',
                    345,
                ),
            ),
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new Test\Fixture\AttributeWithParameters(
                    'qux',
                    234,
                ),
            ),
            Attribute::create(
                Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromClassLocationForClassUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    1,
                ),
            ),
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromClassConstantLocationForClassConstantUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromClassPropertyLocationForClassPropertyUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    234,
                ),
            ),
            Attribute::create(
                Location\ClassPropertyLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\PropertyName::fromString('fooBar'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromClassMethodLocationForClassMethodUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    345,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassMethodLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromClassMethodParameterLocationForClassMethodParameterUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    456,
                ),
            ),
            Attribute::create(
                Location\ClassMethodParameterLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\MethodName::fromString('barBaz'),
                    Name\ParameterName::fromString('bazQux'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromConstantLocationForConstantUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
                new Test\Fixture\AttributeWithParameters(
                    'bar',
                    123,
                ),
            ),
            Attribute::create(
                Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromFunctionLocationForFunctionUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new Test\Fixture\AttributeWithParameters(
                    'qux',
                    234,
                ),
            ),
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'quz',
                    345,
                ),
            ),
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromFunctionParameterLocationForFunctionParameterUsingAttributes(): array
    {
        return [
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithParameters(
                    'quz',
                    345,
                ),
            ),
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        ];
    }
}
