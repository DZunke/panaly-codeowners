<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test\Parser;

use DZunke\PanalyCodeOwners\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testGlobalOwnerOnItsOwn(): void
    {
        $owners = (new Parser())->parse(
            __DIR__ . '/../Fixture/Globs/RootDirectory',
            <<<'TEXT'
            * @globalOwner # just a single person to rule them all
            TEXT,
        );

        self::assertArrayHasKey('@globalOwner', $owners);
        $globalOwner = $owners['@globalOwner'];

        self::assertSame(['FooDir', 'BarDir', 'BarDir/FooDir'], $globalOwner->getPaths());
        self::assertSame(
            ['FooDir/bar.js', 'baz.php', 'foo.txt', 'root.js', 'BarDir/FooDir/my.html'],
            $globalOwner->getFiles(),
        );
    }

    public function testGlobalOwnerIsOverwrittenBySpecificOwnersAndOrderCounts(): void
    {
        $owners = (new Parser())->parse(
            __DIR__ . '/../Fixture/Globs/RootDirectory',
            <<<'TEXT'
            * @globalOwner      # just a single person to rule them all
            *.js @jsOwner       # a specific owner to take over a specific area from the global owner
            FooDir @fooOwner    # a subdirectory that can be found multiple times in the repo
            TEXT,
        );

        self::assertArrayHasKey('@globalOwner', $owners);
        $globalOwner = $owners['@globalOwner'];

        self::assertSame(['BarDir'], $globalOwner->getPaths());
        self::assertSame(['baz.php', 'foo.txt'], $globalOwner->getFiles());

        self::assertArrayHasKey('@jsOwner', $owners);
        $jsOwner = $owners['@jsOwner'];

        self::assertSame([], $jsOwner->getPaths());
        self::assertSame(['root.js'], $jsOwner->getFiles()); // Only one file, because FooDir overwrites the subdir

        self::assertArrayHasKey('@fooOwner', $owners);
        $fooOwner = $owners['@fooOwner'];

        self::assertSame(['FooDir', 'BarDir/FooDir'], $fooOwner->getPaths());
        // Has a js file because defined AFTER *.js owner
        self::assertSame(['FooDir/bar.js', 'BarDir/FooDir/my.html'], $fooOwner->getFiles());
    }
}
