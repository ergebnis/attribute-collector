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
    use Test\Util\Helper;

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

        $collector = new Collector\TraversingAttributeFromFinderCollector();

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

        $collector = new Collector\TraversingAttributeFromFinderCollector();

        $this->expectException(Exception\FinderContainsFileThatCouldNotBeParsed::class);

        $collector->collectFromFinder($finder);
    }

    public function testCollectFromFinderReturnsEmptyAttributeCollectionWhenNoClassesUsingAttributesHaveBeenFound(): void
    {
        $finder = new \ArrayIterator([
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassNotUsingAttributes.php'),
        ]);

        $collector = new Collector\TraversingAttributeFromFinderCollector();

        $collection = $collector->collectFromFinder($finder);

        self::assertSame([], $collection->toArray());
    }

    public function testCollectFromFinderReturnsAttributeCollectionWhenClassUsingUsingAttributesHaveBeenFound(): void
    {
        $finder = new \ArrayIterator([
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassNotUsingAttributes.php'),
            new \SplFileInfo(__DIR__ . '/../../Fixture/ClassUsingAttributes.php'),
        ]);

        $collector = new Collector\TraversingAttributeFromFinderCollector();

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

        $collector = new Collector\TraversingAttributeFromFinderCollector();

        $collection = $collector->collectFromFinder($finder);

        $expected = self::attributesFromClassNameForClassUsingAttributes();

        self::assertEquals($expected, $collection->toArray());
    }

    /**
     * @return list<Attribute>
     */
    private static function attributesFromClassNameForClassUsingAttributes(): array
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

    private static function fileWithParseError(): string
    {
        return \sprintf(
            '%s/source.php',
            self::temporaryDirectory(),
        );
    }
}
