<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

class Owner
{
    public function __construct(
        private readonly string $owner,
        private array $pattern = [],
        private array $paths = [],
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

    public function addPath(string $path): void
    {
        $this->paths[] = $path;
    }

    /** @return list<string> */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public function addFile(string $file): void
    {
        $this->files[] = $file;
    }

    /** @return list<string> */
    public function getFiles(): array
    {
        return $this->files;
    }
}
