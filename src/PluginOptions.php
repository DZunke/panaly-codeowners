<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use DZunke\PanalyCodeOwners\Exception\InvalidOptionGiven;
use DZunke\PanalyCodeOwners\PluginOptions\ReplaceMetricOption;

use function array_filter;
use function current;
use function is_readable;

readonly class PluginOptions
{
    /**
     * @param non-empty-string          $codeOwnerFile
     * @param list<ReplaceMetricOption> $replaceMetricOptions
     */
    public function __construct(
        public string $codeOwnerFile,
        public array $replaceMetricOptions,
    ) {
        if (! is_readable($this->codeOwnerFile)) {
            throw InvalidOptionGiven::codeOwnersFileNotReadable($this->codeOwnerFile);
        }

        if ($this->replaceMetricOptions === []) {
            throw InvalidOptionGiven::replaceMetricOptionIsEmpty();
        }
    }

    public function getMetricOptionsByIdentifier(string $identifier): ReplaceMetricOption|null
    {
        $metricOptions = array_filter(
            $this->replaceMetricOptions,
            static fn (ReplaceMetricOption $option) => $option->metricPath === $identifier,
        );

        $found = current($metricOptions);

        return $found instanceof ReplaceMetricOption ? $found : null;
    }

    public static function fromArray(array $options): PluginOptions
    {
        return new self(
            $options['codeowners'] ?? 'CODEOWNERS',
            self::fromArrayConvertedReplaceMetricOptions($options['replace'] ?? []),
        );
    }

    /**
     * @param list<array{metric: string, option: string, owners: list<string>}> $replace
     *
     * @return list<ReplaceMetricOption>
     */
    private static function fromArrayConvertedReplaceMetricOptions(array $replace): array
    {
        $replaceMetricOptions = [];
        foreach ($replace as $replaceOptions) {
            $replaceMetricOptions[] = new ReplaceMetricOption(
                $replaceOptions['metric'],
                $replaceOptions['option'],
                $replaceOptions['owners'],
            );
        }

        return $replaceMetricOptions;
    }
}
