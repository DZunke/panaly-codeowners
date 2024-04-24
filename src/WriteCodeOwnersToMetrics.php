<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use DZunke\PanalyCodeOwners\Parser\Parser;
use DZunke\PanalyCodeOwners\PluginOptions\ReplaceMetricOption;
use Panaly\Event\BeforeMetricCalculate;

use function array_key_exists;
use function array_merge;
use function assert;
use function file_get_contents;
use function getcwd;
use function is_string;

readonly class WriteCodeOwnersToMetrics
{
    public function __construct(
        private PluginOptions $options,
        private Parser $parser,
    ) {
    }

    public function __invoke(BeforeMetricCalculate $event): void
    {
        $optionForReplacement = $this->options->getMetricOptionsByIdentifier($event->metricConfiguration->identifier);
        if (! $optionForReplacement instanceof ReplaceMetricOption) {
            return;
        }

        $codeownerContent = file_get_contents($this->options->codeOwnerFile);
        assert(is_string($codeownerContent));

        $cwdPath = getcwd();
        assert(is_string($cwdPath));

        $pathsGroupedByOwners = $this->parser->parse(
            $cwdPath,
            $codeownerContent,
        );

        $pathsToBeSet = [];
        foreach ($optionForReplacement->owners as $owner) {
            if (! array_key_exists($owner, $pathsGroupedByOwners)) {
                continue;
            }

            $pathsToBeSet = array_merge(
                $pathsToBeSet,
                $pathsGroupedByOwners[$owner]->getPaths(),
                $pathsGroupedByOwners[$owner]->getFiles(),
            );
        }

        $event->setOption($optionForReplacement->option, $pathsToBeSet);
    }
}
