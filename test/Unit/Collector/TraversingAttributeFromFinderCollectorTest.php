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
 * @covers \Ergebnis\AttributeCollector\Collector\TraversingAttributeFromFinderCollector
 *
 * @uses \Ergebnis\AttributeCollector\Attribute
 * @uses \Ergebnis\AttributeCollector\AttributeCollection
 * @uses \Ergebnis\AttributeCollector\Collector\TraversingAttributeFromClassNameCollector
 * @uses \Ergebnis\AttributeCollector\Collector\VisitedLocationCollection
 * @uses \Ergebnis\AttributeCollector\Exception\ClassCouldNotBeReflected
 * @uses \Ergebnis\AttributeCollector\Exception\FinderContainsFileThatCouldNotBeParsed
 * @uses \Ergebnis\AttributeCollector\Location\ClassConstantLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassMethodParameterLocation
 * @uses \Ergebnis\AttributeCollector\Location\ClassPropertyLocation
 * @uses \Ergebnis\AttributeCollector\Name\ClassName
 * @uses \Ergebnis\AttributeCollector\Name\ConstantName
 * @uses \Ergebnis\AttributeCollector\Name\MethodName
 * @uses \Ergebnis\AttributeCollector\Name\ParameterName
 * @uses \Ergebnis\AttributeCollector\Name\PropertyName
 */
final class TraversingAttributeFromFinderCollectorTest extends Framework\TestCase
{
    use AttributeCollector\Test\Util\Helper;

    protected function setUp(): void
    {
        self::filesystem()->mkdir(self::temporaryDirectory());

        $source = <<<'TXT'
            <?php

            final class MessedUp
            {
            TXT;

        self::filesystem()->dumpFile(
            self::fileWithParseError(),
            $source,
        );
    }

    protected function tearDown(): void
    {
        self::filesystem()->remove(self::temporaryDirectory());
    }

    public function testCollectFromFinderReturnsEmptyAttributeCollectionWhenFinderIsEmpty(): void
    {
        $finder = new \ArrayIterator();

        $collector = new AttributeCollector\Collector\TraversingAttributeFromFinderCollector();

        $collection = $collector->collectFromFinder($finder);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromFinderThrowsFinderContainsFileThatCouldNotBeParsedWhenFinderContainsFileThatCouldNotBeParsed(): void
    {
        $finder = new \ArrayIterator([
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassNotUsingAttributes.php'),
            new \SplFileInfo(self::fileWithParseError()),
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassUsingAttributes.php'),
        ]);

        $collector = new AttributeCollector\Collector\TraversingAttributeFromFinderCollector();

        $this->expectException(AttributeCollector\Exception\FinderContainsFileThatCouldNotBeParsed::class);

        $collector->collectFromFinder($finder);
    }

    public function testCollectFromFinderReturnsEmptyAttributeCollectionWhenNoClassesUsingAttributesHaveBeenFound(): void
    {
        $finder = new \ArrayIterator([
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassNotUsingAttributes.php'),
        ]);

        $collector = new AttributeCollector\Collector\TraversingAttributeFromFinderCollector();

        $collection = $collector->collectFromFinder($finder);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromFinderReturnsAttributeCollectionWhenClassUsingUsingAttributesHaveBeenFound(): void
    {
        $finder = new \ArrayIterator([
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassNotUsingAttributes.php'),
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassUsingAttributes.php'),
        ]);

        $collector = new AttributeCollector\Collector\TraversingAttributeFromFinderCollector();

        $collection = $collector->collectFromFinder($finder);

        $expected = self::attributesFromClassNameForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    public function testCollectFromFinderReturnsAttributeCollectionWhenDuplicateClassesUsingAttributesHaveBeenFound(): void
    {
        $finder = new \ArrayIterator([
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassUsingAttributes.php'),
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassNotUsingAttributes.php'),
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassUsingAttributes.php'),
        ]);

        $collector = new AttributeCollector\Collector\TraversingAttributeFromFinderCollector();

        $collection = $collector->collectFromFinder($finder);

        $expected = self::attributesFromClassNameForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @return list<AttributeCollector\Attribute>
     */
    private static function attributesFromClassNameForClassUsingAttributes(): array
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

    private static function fileWithParseError(): string
    {
        return \sprintf(
            '%s/source.php',
            self::temporaryDirectory(),
        );
    }
}
