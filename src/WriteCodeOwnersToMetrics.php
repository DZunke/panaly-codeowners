<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use DZunke\PanalyCodeOwners\PluginOptions\ReplaceMetricOption;
use Panaly\Event\BeforeMetricCalculate;

use function array_key_exists;
use function array_merge;

readonly class WriteCodeOwnersToMetrics
{
    public function __construct(
        private PluginOptions $options,
        private CodeOwnerParser $parser,
    ) {
    }

    public function __invoke(BeforeMetricCalculate $event): void
    {
        $optionForReplacement = $this->options->getMetricOptionsByIdentifier($event->metricConfiguration->identifier);
        if (! $optionForReplacement instanceof ReplaceMetricOption) {
            return;
        }

        $pathsGroupedByOwners = $this->parser->parse($this->options->codeOwnerFile);

        $pathsToBeSet = [];
        foreach ($optionForReplacement->owners as $owner) {
            if (! array_key_exists($owner, $pathsGroupedByOwners)) {
                continue;
            }

            $pathsToBeSet = array_merge($pathsToBeSet, $pathsGroupedByOwners[$owner]->getPaths());
        }

        $event->setOption($optionForReplacement->option, $pathsToBeSet);
    }
}
