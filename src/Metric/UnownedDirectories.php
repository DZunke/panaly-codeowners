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
use function assert;
use function file_get_contents;
use function getcwd;
use function is_string;

class UnownedDirectories implements Metric
{
    public function __construct(
        private readonly Parser $parser,
        private readonly PluginOptions $pluginOptions,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'unowned_directories';
    }

    public function getDefaultTitle(): string
    {
        return 'Directories without Ownership';
    }

    public function calculate(array $options): Value
    {
        $codeownerContent = file_get_contents($this->pluginOptions->codeOwnerFile);
        assert(is_string($codeownerContent));

        $cwdPath = getcwd();
        assert(is_string($cwdPath));

        $owners = $this->parser->parse(
            new Configuration($cwdPath),
            $codeownerContent,
        );

        if (! array_key_exists(Parser::UNOWNED, $owners)) {
            return new Table(['file'], []);
        }

        return new Table(
            ['file'],
            array_map(static fn (string $file) => [$file], $owners[Parser::UNOWNED]->getRelativePaths()),
        );
    }
}
