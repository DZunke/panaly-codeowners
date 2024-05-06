<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test;

use DZunke\PanalyCodeOwners\Owner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class OwnerTest extends TestCase
{
    public function testBuildCreateOwner(): void
    {
        $owner = $this->createOwner();

        self::assertSame('@foo', $owner->getOwner());
        self::assertSame(['Fixture/Globs/RootDirectory'], $owner->getPattern());

        self::assertCount(2, $owner->getFiles());
        self::assertSame(
            [
                'Fixture/Globs/RootDirectory/foo.txt',
                'Fixture/Globs/RootDirectory/baz.php',
            ],
            $owner->getRelativeFiles(),
        );

        self::assertSame(
            [
                __DIR__ . '/Fixture/Globs/RootDirectory/foo.txt',
                __DIR__ . '/Fixture/Globs/RootDirectory/baz.php',
            ],
            $owner->getAbsoluteFiles(),
        );

        self::assertCount(1, $owner->getPaths());
        self::assertSame(['Fixture/Globs/RootDirectory'], $owner->getRelativePaths());
        self::assertSame([__DIR__ . '/Fixture/Globs/RootDirectory'], $owner->getAbsolutePaths());
    }

    private function createOwner(): Owner
    {
        return new Owner(
            '@foo',
            ['Fixture/Globs/RootDirectory'],
            [
                new SplFileInfo(
                    __DIR__ . '/Fixture/Globs/RootDirectory',
                    'Fixture/Globs/RootDirectory',
                    'Fixture/Globs/RootDirectory',
                ),
            ],
            [
                new SplFileInfo(
                    __DIR__ . '/Fixture/Globs/RootDirectory/foo.txt',
                    'Fixture/Globs/RootDirectory',
                    'Fixture/Globs/RootDirectory/foo.txt',
                ),
                new SplFileInfo(
                    __DIR__ . '/Fixture/Globs/RootDirectory/baz.php',
                    'Fixture/Globs/RootDirectory',
                    'Fixture/Globs/RootDirectory/baz.php',
                ),
            ],
        );
    }
}
