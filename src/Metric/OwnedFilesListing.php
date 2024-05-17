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
use function getcwd;
use function is_array;
use function is_string;

class OwnedFilesListing implements Metric
{
    public function __construct(
        private readonly Parser $parser,
        private readonly PluginOptions $pluginOptions,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'owned_files_list';
    }

    public function getDefaultTitle(): string
    {
        return 'Owned Files List';
    }

    public function calculate(array $options): Value
    {
        if (! array_key_exists('owners', $options) || ! is_array($options['owners']) || count($options['owners']) === 0) {
            return new Table(['file'], []);
        }

        $codeownerContent = file_get_contents($this->pluginOptions->codeOwnerFile);
        assert(is_string($codeownerContent));

        $cwdPath = getcwd();
        assert(is_string($cwdPath));

        $owners = $this->parser->parse(
            new Configuration($cwdPath),
            $codeownerContent,
        );

        $files = [];
        foreach ($options['owners'] as $owner) {
            if (! array_key_exists($owner, $owners)) {
                continue;
            }

            $files = array_merge($files, array_values($owners[$owner]->getRelativeFiles()));
        }

        return new Table(
            ['file'],
            array_map(static fn (string $file) => [$file], $files),
        );
    }
}
