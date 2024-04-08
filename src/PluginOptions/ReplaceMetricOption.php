<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\PluginOptions;

use DZunke\PanalyCodeOwners\Exception\InvalidOptionGiven;

readonly class ReplaceMetricOption
{
    public function __construct(
        public string $metricPath,
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
