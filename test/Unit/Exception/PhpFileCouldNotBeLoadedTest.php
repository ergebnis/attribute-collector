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

namespace Ergebnis\AttributeCollector\Test\Unit\Exception;

use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AttributeCollector\Exception\PhpFileCouldNotBeLoaded
 */
final class PhpFileCouldNotBeLoadedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testForFileNameThatDoesNotExistReturnsException(): void
    {
        $fileName = self::faker()->sentence();

        $exception = Exception\PhpFileCouldNotBeLoaded::forFileNameThatDoesNotExist($fileName);

        $message = \sprintf(
            'PHP file "%s" could not be loaded because it does not exist.',
            $fileName,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testForFileNameThatIsNotReadableReturnsException(): void
    {
        $fileName = self::faker()->sentence();

        $exception = Exception\PhpFileCouldNotBeLoaded::forFileNameThatIsNotReadable($fileName);

        $message = \sprintf(
            'PHP file "%s" could not be loaded because it is not readable.',
            $fileName,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testForFileNameThatDoesNotReturnAttributeCollectionReturnsException(): void
    {
        $fileName = self::faker()->sentence();

        $exception = Exception\PhpFileCouldNotBeLoaded::forFileNameThatDoesNotReturnAttributeCollection($fileName);

        $message = \sprintf(
            'PHP file "%s" could not be loaded because it does not return an AttributeCollection.',
            $fileName,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
