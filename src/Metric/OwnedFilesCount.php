<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Metric;

use DZunke\PanalyCodeOwners\Parser\Configuration;
use DZunke\PanalyCodeOwners\Parser\Parser;
use DZunke\PanalyCodeOwners\PluginOptions;
use Panaly\Plugin\Plugin\Metric;
use Panaly\Result\Metric\IntegerValue;
use Panaly\Result\Metric\Value;

use function array_key_exists;
use function assert;
use function count;
use function file_get_contents;
use function is_array;
use function is_string;

class OwnedFilesCount implements Metric
{
    public function __construct(
        private readonly Parser $parser,
        private readonly PluginOptions $pluginOptions,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'owned_files_count';
    }

    public function getDefaultTitle(): string
    {
        return 'Owned Files Count';
    }

    public function calculate(array $options): Value
    {
        if (! array_key_exists('owners', $options) || ! is_array($options['owners']) || count($options['owners']) === 0) {
            return new IntegerValue(0);
        }

        $codeownerContent = file_get_contents($this->pluginOptions->codeOwnerFile);
        assert(is_string($codeownerContent));

        $owners = $this->parser->parse(
            new Configuration(),
            $codeownerContent,
        );

        $fileCount = 0;
        foreach ($options['owners'] as $owner) {
            if (! array_key_exists($owner, $owners)) {
                continue;
            }

            $fileCount += count($owners[$owner]->getFiles());
        }

        return new IntegerValue($fileCount);
    }
}
