<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\PluginOptions;

use DZunke\PanalyCodeOwners\Exception\InvalidOptionGiven;

readonly class ReplaceMetricOption
{
    public const TYPE_RELATIVE = 'relative';
    public const TYPE_ABSOLUTE = 'absolute';

    public const WRITE_FILES = 'files';
    public const WRITE_PATHS = 'paths';
    public const WRITE_BOTH  = 'both';

    public function __construct(
        public string $metricPath,
        public string $type,
        public string $write,
        public string $option,
        public array $owners,
    ) {
        if ($this->metricPath === '') {
            throw InvalidOptionGiven::metricPathIsEmpty();
        }

        if ($this->option === '') {
            throw InvalidOptionGiven::metricOptionIsEmpty();
        }

        if ($this->owners === []) {
            throw InvalidOptionGiven::atLeastASingleOwnerGroupMustBeGiven();
        }
    }
}
