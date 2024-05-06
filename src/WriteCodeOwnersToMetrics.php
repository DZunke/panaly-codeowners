<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use DZunke\PanalyCodeOwners\Parser\Configuration;
use DZunke\PanalyCodeOwners\Parser\Parser;
use DZunke\PanalyCodeOwners\PluginOptions\ReplaceMetricOption;
use Panaly\Event\BeforeMetricCalculate;

use function array_key_exists;
use function array_merge;
use function assert;
use function file_get_contents;
use function getcwd;
use function is_string;

class WriteCodeOwnersToMetrics
{
    public function __construct(
        private readonly PluginOptions $options,
        private readonly Parser $parser,
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
            new Configuration(rootPath: $cwdPath, excludeDirectories: $this->options->excludeDirectories),
            $codeownerContent,
        );

        $pathsToBeSet = [];
        foreach ($optionForReplacement->owners as $owner) {
            if (! array_key_exists($owner, $pathsGroupedByOwners)) {
                continue;
            }

            $owner = $pathsGroupedByOwners[$owner];

            $pathsToBeSet = array_merge(
                $pathsToBeSet,
                $this->getFilesAndPathsFromOwner($owner, $optionForReplacement),
            );
        }

        $event->setOption($optionForReplacement->option, $pathsToBeSet);
    }

    /** @return string[] */
    private function getFilesAndPathsFromOwner(
        Owner $owner,
        ReplaceMetricOption $metricOption,
    ): array {
        return match ($metricOption->write) {
            ReplaceMetricOption::WRITE_FILES => $this->getFilesFromOwner($owner, $metricOption),
            ReplaceMetricOption::WRITE_PATHS => $this->getPathsFromOwner($owner, $metricOption),
            ReplaceMetricOption::WRITE_BOTH => array_merge(
                $this->getPathsFromOwner($owner, $metricOption),
                $this->getFilesFromOwner($owner, $metricOption),
            ),
            default => [],
        };
    }

    /** @return string[] */
    private function getFilesFromOwner(Owner $owner, ReplaceMetricOption $metricOption): array
    {
        if ($metricOption->type === ReplaceMetricOption::TYPE_RELATIVE) {
            return $owner->getRelativeFiles();
        }

        return $owner->getAbsoluteFiles();
    }

    /** @return string[] */
    private function getPathsFromOwner(Owner $owner, ReplaceMetricOption $metricOption): array
    {
        if ($metricOption->type === ReplaceMetricOption::TYPE_RELATIVE) {
            return $owner->getRelativePaths();
        }

        return $owner->getAbsolutePaths();
    }
}
