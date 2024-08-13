<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Parser;

readonly class Configuration
{
    /** @param string[] $excludeDirectories */
    public function __construct(
        public bool $ignoreDotFiles = true,
        public array $excludeDirectories = ['vendor'],
    ) {
    }
}
