<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test;

use CodeOwners\Parser;
use DZunke\PanalyCodeOwners\CodeOwnerParser;
use DZunke\PanalyCodeOwners\Exception\FileParsingFailed;
use PHPUnit\Framework\TestCase;

use function array_keys;

class CodeOwnerParserTest extends TestCase
{
    public function testParsingOfAnUnknownFileFails(): void
    {
        $this->expectException(FileParsingFailed::class);

        $parser = new CodeOwnerParser(new Parser());
        $parser->parse('foo bar baz');
    }

    public function testThatParsingIsWorking(): void
    {
        $parser     = new CodeOwnerParser(new Parser());
        $ownerFiles = $parser->parse(__DIR__ . '/Fixture/CODEOWNERS');

        self::assertCount(4, $ownerFiles);
        self::assertSame(['@IronMan', '@Hulk', '@BlackWidow', '@DrStrange'], array_keys($ownerFiles));

        self::assertSame('@IronMan', $ownerFiles['@IronMan']->getOwner());
        self::assertSame(['src/Exception', 'composer.json'], $ownerFiles['@IronMan']->getPaths());
    }
}
