<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use DZunke\PanalyCodeOwners\Metric\OwnedDirectoriesCount;
use DZunke\PanalyCodeOwners\Metric\OwnedFilesCount;
use DZunke\PanalyCodeOwners\Metric\UnownedDirectories;
use DZunke\PanalyCodeOwners\Parser\Parser;
use Panaly\Configuration\ConfigurationFile;
use Panaly\Configuration\RuntimeConfiguration;
use Panaly\Event\BeforeMetricCalculate;
use Panaly\Plugin\BasePlugin;

class CodeOwnersPlugin extends BasePlugin
{
    private PluginOptions|null $pluginOptions;
    private Parser|null $parser;

    public function initialize(
        ConfigurationFile $configurationFile,
        RuntimeConfiguration $runtimeConfiguration,
        array $options,
    ): void {
        $this->parser = new Parser();
        $this->parser->setLogger($runtimeConfiguration->getLogger());

        $runtimeConfiguration->getEventDispatcher()->addListener(
            BeforeMetricCalculate::class,
            new WriteCodeOwnersToMetrics(
                $this->pluginOptions = PluginOptions::fromArray($options),
                $this->parser,
            ),
        );
    }

    /** @inheritDoc */
    public function getAvailableMetrics(array $options): array
    {
        if (! $this->parser instanceof Parser || ! $this->pluginOptions instanceof PluginOptions) {
            // If one of the required things are not available there will be no metric available
            return [];
        }

        return [
            new UnownedDirectories($this->parser, $this->pluginOptions),
            new OwnedFilesCount($this->parser, $this->pluginOptions),
            new OwnedDirectoriesCount($this->parser, $this->pluginOptions),
        ];
    }
}
