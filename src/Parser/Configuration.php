<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Parser;

readonly class Configuration
{
    public function __construct(
        public string $rootPath,
        public bool $ignoreDotFiles = true,
        public array $excludeDirectories = ['vendor'],
    ) {
    }
}
