<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use Symfony\Component\Finder\SplFileInfo;

use function array_map;

class Owner
{
    public function __construct(
        private readonly string $owner,
        private array $pattern = [],
        /** @var list<SplFileInfo> */
        private array $paths = [],
        /** @var list<SplFileInfo> */
        private array $files = [],
    ) {
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function addPattern(string $pattern): void
    {
        $this->pattern[] = $pattern;
    }

    /** @return list<string> */
    public function getPattern(): array
    {
        return $this->pattern;
    }

    public function addPath(SplFileInfo $fileInfo): void
    {
        $this->paths[] = $fileInfo;
    }

    /** @return list<SplFileInfo> */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /** @return list<string> */
    public function getRelativePaths(): array
    {
        return array_map(
            static fn (SplFileInfo $fileInfo) => $fileInfo->getRelativePathname(),
            $this->paths,
        );
    }

    /** @return list<string> */
    public function getAbsolutePaths(): array
    {
        return array_map(
            static fn (SplFileInfo $fileInfo) => $fileInfo->getRealPath(),
            $this->paths,
        );
    }

    public function addFile(SplFileInfo $fileInfo): void
    {
        $this->files[] = $fileInfo;
    }

    /** @return list<SplFileInfo> */
    public function getFiles(): array
    {
        return $this->files;
    }

    /** @return list<string> */
    public function getRelativeFiles(): array
    {
        return array_map(
            static fn (SplFileInfo $fileInfo) => $fileInfo->getRelativePathname(),
            $this->files,
        );
    }

    /** @return list<string> */
    public function getAbsoluteFiles(): array
    {
        return array_map(
            static fn (SplFileInfo $fileInfo) => $fileInfo->getRealPath(),
            $this->files,
        );
    }
}
