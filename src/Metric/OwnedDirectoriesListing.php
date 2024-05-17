<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Metric;

use DZunke\PanalyCodeOwners\Parser\Configuration;
use DZunke\PanalyCodeOwners\Parser\Parser;
use DZunke\PanalyCodeOwners\PluginOptions;
use Panaly\Plugin\Plugin\Metric;
use Panaly\Result\Metric\Table;
use Panaly\Result\Metric\Value;

use function array_key_exists;
use function array_map;
use function array_merge;
use function array_values;
use function assert;
use function count;
use function file_get_contents;
use function is_array;
use function is_string;

class OwnedDirectoriesListing implements Metric
{
    public function __construct(
        private readonly Parser $parser,
        private readonly PluginOptions $pluginOptions,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'owned_directories_list';
    }

    public function getDefaultTitle(): string
    {
        return 'Owned Directories List';
    }

    public function calculate(array $options): Value
    {
        if (! array_key_exists('owners', $options) || ! is_array($options['owners']) || count($options['owners']) === 0) {
            return new Table(['directory'], []);
        }

        $codeownerContent = file_get_contents($this->pluginOptions->codeOwnerFile);
        assert(is_string($codeownerContent));

        $owners = $this->parser->parse(
            new Configuration(),
            $codeownerContent,
        );

        $directories = [];
        foreach ($options['owners'] as $owner) {
            if (! array_key_exists($owner, $owners)) {
                continue;
            }

            $directories = array_merge($directories, array_values($owners[$owner]->getRelativePaths()));
        }

        return new Table(
            ['directory'],
            array_map(static fn (string $directory) => [$directory], $directories),
        );
    }
}
