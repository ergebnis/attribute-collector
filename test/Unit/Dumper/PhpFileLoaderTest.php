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

use Ergebnis\AttributeCollector\Dumper;
use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Dumper\PhpFileLoader
 *
 * @uses \Ergebnis\AttributeCollector\Exception\PhpFileCouldNotBeLoaded
 */
final class PhpFileLoaderTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testLoadThrowsExceptionWhenFileDoesNotExist(): void
    {
        $loader = new Dumper\PhpFileLoader();

        $fileName = self::temporaryDirectory() . '/does-not-exist-' . \bin2hex(\random_bytes(8)) . '.php';

        $this->expectException(Exception\PhpFileCouldNotBeLoaded::class);

        $loader->load($fileName);
    }

    public function testLoadThrowsExceptionWhenFileDoesNotReturnAttributeCollection(): void
    {
        $loader = new Dumper\PhpFileLoader();

        $fileName = self::temporaryDirectory() . '/not-attribute-collection-' . \bin2hex(\random_bytes(8)) . '.php';

        self::filesystem()->dumpFile($fileName, "<?php\n\nreturn 'not an attribute collection';\n");

        try {
            $this->expectException(Exception\PhpFileCouldNotBeLoaded::class);

            $loader->load($fileName);
        } finally {
            self::filesystem()->remove($fileName);
        }
    }
}
