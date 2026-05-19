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

namespace Ergebnis\AttributeCollector\Test\Unit\Dumper;

use Ergebnis\AttributeCollector\Attribute;
use Ergebnis\AttributeCollector\AttributeCollection;
use Ergebnis\AttributeCollector\Dumper;
use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Location;
use Ergebnis\AttributeCollector\Name;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Dumper\PhpFileDumper
 *
 * @uses \Ergebnis\AttributeCollector\Attribute
 * @uses \Ergebnis\AttributeCollector\AttributeCollection
 * @uses \Ergebnis\AttributeCollector\Dumper\PhpFileLoader
 * @uses \Ergebnis\AttributeCollector\Exception\AttributeInstanceCouldNotBeExported
 * @uses \Ergebnis\AttributeCollector\Exception\PhpFileCouldNotBeLoaded
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
final class PhpFileDumperTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDumpReturnsPhpFileContentForEmptyCollection(): void
    {
        $dumper = new Dumper\PhpFileDumper();

        $collection = AttributeCollection::create();

        $content = $dumper->dump($collection);

        $expected = <<<'PHP'
<?php

declare(strict_types=1);

return \Ergebnis\AttributeCollector\AttributeCollection::create();

PHP;

        self::assertSame($expected, $content);
    }

    public function testDumpReturnsPhpFileContentForCollectionWithAttributeWithoutParameters(): void
    {
        $dumper = new Dumper\PhpFileDumper();

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithoutParameters::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        );

        $content = $dumper->dump($collection);

        self::assertStringContainsString('new \Ergebnis\AttributeCollector\Test\Fixture\AttributeWithoutParameters()', $content);
        self::assertStringContainsString('ClassLocation::create', $content);
    }

    public function testDumpReturnsPhpFileContentForCollectionWithAttributeWithParameters(): void
    {
        $dumper = new Dumper\PhpFileDumper();

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithParameters::class)),
                new Test\Fixture\AttributeWithParameters(
                    'hello',
                    42,
                ),
            ),
        );

        $content = $dumper->dump($collection);

        self::assertStringContainsString("new \\Ergebnis\\AttributeCollector\\Test\\Fixture\\AttributeWithParameters('hello', 42)", $content);
    }

    public function testDumpReturnsPhpFileContentForCollectionWithMultipleLocationTypes(): void
    {
        $dumper = new Dumper\PhpFileDumper();

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithParameters('bar', 123),
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
                new Test\Fixture\AttributeWithParameters('baz', 456),
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
                Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        );

        $content = $dumper->dump($collection);

        self::assertStringContainsString('ClassLocation::create', $content);
        self::assertStringContainsString('ClassConstantLocation::create', $content);
        self::assertStringContainsString('ClassPropertyLocation::create', $content);
        self::assertStringContainsString('ClassMethodLocation::create', $content);
        self::assertStringContainsString('ClassMethodParameterLocation::create', $content);
        self::assertStringContainsString('ConstantLocation::create', $content);
        self::assertStringContainsString('FunctionLocation::create', $content);
        self::assertStringContainsString('FunctionParameterLocation::create', $content);
    }

    public function testDumpAndLoadRoundTripForEmptyCollection(): void
    {
        $dumper = new Dumper\PhpFileDumper();
        $loader = new Dumper\PhpFileLoader();

        $collection = AttributeCollection::create();

        $content = $dumper->dump($collection);

        $fileName = self::temporaryDirectory() . '/dump-empty-' . \bin2hex(\random_bytes(8)) . '.php';

        self::filesystem()->dumpFile($fileName, $content);

        $loadedCollection = $loader->load($fileName);

        self::assertEquals($collection->toArray(), $loadedCollection->toArray());

        self::filesystem()->remove($fileName);
    }

    public function testDumpAndLoadRoundTripForCollectionWithAttributeWithoutParameters(): void
    {
        $dumper = new Dumper\PhpFileDumper();
        $loader = new Dumper\PhpFileLoader();

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithoutParameters::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        );

        $content = $dumper->dump($collection);

        $fileName = self::temporaryDirectory() . '/dump-without-params-' . \bin2hex(\random_bytes(8)) . '.php';

        self::filesystem()->dumpFile($fileName, $content);

        $loadedCollection = $loader->load($fileName);

        self::assertCount(\count($collection->toArray()), $loadedCollection->toArray());

        $originalAttribute = $collection->toArray()[0];
        $loadedAttribute = $loadedCollection->toArray()[0];

        self::assertTrue($originalAttribute->location()->equals($loadedAttribute->location()));
        self::assertInstanceOf(Test\Fixture\AttributeWithoutParameters::class, $loadedAttribute->instance());

        self::filesystem()->remove($fileName);
    }

    public function testDumpAndLoadRoundTripForCollectionWithAttributeWithParameters(): void
    {
        $dumper = new Dumper\PhpFileDumper();
        $loader = new Dumper\PhpFileLoader();

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributeWithParameters::class)),
                new Test\Fixture\AttributeWithParameters(
                    'hello',
                    42,
                ),
            ),
        );

        $content = $dumper->dump($collection);

        $fileName = self::temporaryDirectory() . '/dump-with-params-' . \bin2hex(\random_bytes(8)) . '.php';

        self::filesystem()->dumpFile($fileName, $content);

        $loadedCollection = $loader->load($fileName);

        self::assertCount(1, $loadedCollection->toArray());

        $originalAttribute = $collection->toArray()[0];
        $loadedAttribute = $loadedCollection->toArray()[0];

        self::assertTrue($originalAttribute->location()->equals($loadedAttribute->location()));

        $loadedInstance = $loadedAttribute->instance();

        self::assertInstanceOf(Test\Fixture\AttributeWithParameters::class, $loadedInstance);

        /** @var Test\Fixture\AttributeWithParameters $loadedInstance */
        self::assertSame('hello', $loadedInstance->foo());
        self::assertSame(42, $loadedInstance->bar());

        self::filesystem()->remove($fileName);
    }

    public function testDumpAndLoadRoundTripForCollectionWithMultipleAttributes(): void
    {
        $dumper = new Dumper\PhpFileDumper();
        $loader = new Dumper\PhpFileLoader();

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\ClassConstantLocation::create(
                    Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class),
                    Name\ConstantName::fromString('FOO'),
                ),
                new Test\Fixture\AttributeWithParameters('bar', 123),
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
                new Test\Fixture\AttributeWithParameters('baz', 456),
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
                Location\ConstantLocation::create(Name\ConstantName::fromString('Ergebnis\AttributeCollector\Test\Fixture\FOO')),
                new Test\Fixture\AttributeWithParameters('const', 789),
            ),
            Attribute::create(
                Location\FunctionLocation::create(Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge')),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
            Attribute::create(
                Location\FunctionParameterLocation::create(
                    Name\FunctionName::fromString('Ergebnis\AttributeCollector\Test\Fixture\quuxCorge'),
                    Name\ParameterName::fromString('corgeGrault'),
                ),
                new Test\Fixture\AttributeWithoutParameters(),
            ),
        );

        $content = $dumper->dump($collection);

        $fileName = self::temporaryDirectory() . '/dump-multiple-' . \bin2hex(\random_bytes(8)) . '.php';

        self::filesystem()->dumpFile($fileName, $content);

        $loadedCollection = $loader->load($fileName);

        self::assertCount(\count($collection->toArray()), $loadedCollection->toArray());

        $originalAttributes = $collection->toArray();
        $loadedAttributes = $loadedCollection->toArray();

        foreach ($originalAttributes as $index => $originalAttribute) {
            self::assertTrue(
                $originalAttribute->location()->equals($loadedAttributes[$index]->location()),
                \sprintf('Location at index %d does not match.', $index),
            );
            self::assertSame(
                $originalAttribute->className()->toString(),
                $loadedAttributes[$index]->className()->toString(),
                \sprintf('Class name at index %d does not match.', $index),
            );
        }

        self::filesystem()->remove($fileName);
    }

    public function testDumpThrowsExceptionWhenAttributeInstanceHasUnsupportedParameterValueType(): void
    {
        $dumper = new Dumper\PhpFileDumper();

        $collection = AttributeCollection::create(
            Attribute::create(
                Location\ClassLocation::create(Name\ClassName::fromString(Test\Fixture\ClassUsingAttributes::class)),
                new Test\Fixture\AttributeWithObjectParameter(new \stdClass()),
            ),
        );

        $this->expectException(Exception\AttributeInstanceCouldNotBeExported::class);

        $dumper->dump($collection);
    }
}
