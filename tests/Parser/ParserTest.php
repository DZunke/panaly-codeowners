<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test\Parser;

use DZunke\PanalyCodeOwners\Parser\Configuration;
use DZunke\PanalyCodeOwners\Parser\Parser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ParserTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset the parser internal cache on every run
        (new ReflectionClass(Parser::class))->setStaticPropertyValue('ownerCache', []);
    }

    public function testUnownedCollection(): void
    {
        $owners = (new Parser())->parse(
            $this->getConfiguration(),
            <<<'TEXT'
            *.go @globalOwner # a single owner but it will not have anything because there is nothing
            TEXT,
        );

        self::assertArrayHasKey('@globalOwner', $owners);
        $globalOwner = $owners['@globalOwner'];

        self::assertCount(0, $globalOwner->getPaths());
        self::assertCount(0, $globalOwner->getFiles());

        self::assertArrayHasKey(Parser::UNOWNED, $owners);
        $unowned = $owners[Parser::UNOWNED];

        self::assertCount(3, $unowned->getPaths());
        self::assertCount(5, $unowned->getFiles());
    }

    public function testGlobalOwnerOnItsOwn(): void
    {
        $owners = (new Parser())->parse(
            $this->getConfiguration(),
            <<<'TEXT'
            * @globalOwner # just a single person to rule them all
            TEXT,
        );

        self::assertArrayHasKey('@globalOwner', $owners);
        $globalOwner = $owners['@globalOwner'];

        self::assertSame(['FooDir', 'BarDir', 'BarDir/FooDir'], $globalOwner->getRelativePaths());
        self::assertSame(
            ['FooDir/bar.js', 'baz.php', 'foo.txt', 'root.js', 'BarDir/FooDir/my.html'],
            $globalOwner->getRelativeFiles(),
        );
    }

    public function testASingleOwnerOfMultipleFileTypes(): void
    {
        $owners = (new Parser())->parse(
            $this->getConfiguration(),
            <<<'TEXT'
            *.php   @fileOwner
            *.html  @fileOwner
            TEXT,
        );

        self::assertArrayHasKey('@fileOwner', $owners);
        $fileOwner = $owners['@fileOwner'];

        self::assertSame([], $fileOwner->getPaths());
        self::assertSame(
            ['baz.php', 'BarDir/FooDir/my.html'],
            $fileOwner->getRelativeFiles(),
        );
    }

    public function testAFileCouldBeOwnedByMultipleOwners(): void
    {
        $owners = (new Parser())->parse(
            $this->getConfiguration(),
            <<<'TEXT'
            *.php       @fileOwner @anotherOwner
            TEXT,
        );

        self::assertArrayHasKey('@fileOwner', $owners);
        $fileOwner = $owners['@fileOwner'];

        self::assertSame([], $fileOwner->getPaths());
        self::assertSame(['baz.php'], $fileOwner->getRelativeFiles());

        self::assertArrayHasKey('@anotherOwner', $owners);
        $anotherOwner = $owners['@anotherOwner'];

        self::assertSame([], $anotherOwner->getPaths());
        self::assertSame(['baz.php'], $anotherOwner->getRelativeFiles());
    }

    public function testOwningADirectoryWithAppendedSlash(): void
    {
        // See Github Specification, the appended slash marks that this directory itself is not owned but it's files
        $owners = (new Parser())->parse(
            $this->getConfiguration(ignoreDotFiles: false),
            <<<'TEXT'
            BarDir/ @recursiveOwner
            TEXT,
        );

        self::assertArrayHasKey('@recursiveOwner', $owners);
        $recursiveOwner = $owners['@recursiveOwner'];

        self::assertSame(['BarDir/FooDir'], $recursiveOwner->getRelativePaths());
        self::assertSame(['BarDir/.gitignore', 'BarDir/FooDir/my.html'], $recursiveOwner->getRelativeFiles());
    }

    public function testGlobalOwnerIsOverwrittenBySpecificOwnersAndOrderCounts(): void
    {
        $owners = (new Parser())->parse(
            $this->getConfiguration(),
            <<<'TEXT'
            * @globalOwner      # just a single person to rule them all
            *.js @jsOwner       # a specific owner to take over a specific area from the global owner
            FooDir @fooOwner    # a subdirectory that can be found multiple times in the repo
            TEXT,
        );

        self::assertArrayHasKey('@globalOwner', $owners);
        $globalOwner = $owners['@globalOwner'];

        self::assertSame(['BarDir'], $globalOwner->getRelativePaths());
        self::assertSame(['baz.php', 'foo.txt'], $globalOwner->getRelativeFiles());

        self::assertArrayHasKey('@jsOwner', $owners);
        $jsOwner = $owners['@jsOwner'];

        self::assertSame([], $jsOwner->getPaths());
        self::assertSame(['root.js'], $jsOwner->getRelativeFiles()); // Only one file, because FooDir overwrites the subdir

        self::assertArrayHasKey('@fooOwner', $owners);
        $fooOwner = $owners['@fooOwner'];

        self::assertSame(['FooDir', 'BarDir/FooDir'], $fooOwner->getRelativePaths());
        // Has a js file because defined AFTER *.js owner
        self::assertSame(['FooDir/bar.js', 'BarDir/FooDir/my.html'], $fooOwner->getRelativeFiles());
    }

    private function getConfiguration(
        bool $ignoreDotFiles = true,
    ): Configuration {
        return new Configuration(
            __DIR__ . '/../Fixture/Globs/RootDirectory',
            $ignoreDotFiles,
        );
    }
}
