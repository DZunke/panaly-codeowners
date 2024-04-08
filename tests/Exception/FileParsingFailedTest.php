<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test\Exception;

use DZunke\PanalyCodeOwners\Exception\FileParsingFailed;
use PHPUnit\Framework\TestCase;
use Throwable;

class FileParsingFailedTest extends TestCase
{
    public function testFileCouldNotBeParsed(): void
    {
        $previous  = self::createStub(Throwable::class);
        $exception = FileParsingFailed::fileCouldNotBeParsed('foo', $previous);

        self::assertSame('The file "foo" could not be parsed to owner configuration', $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }
}
