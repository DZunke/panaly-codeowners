<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

class Owner
{
    public function __construct(
        private readonly string $owner,
        private array $paths = [],
    ) {
    }

    public function getOwner(): string
    {
        return $this->owner;
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
}
