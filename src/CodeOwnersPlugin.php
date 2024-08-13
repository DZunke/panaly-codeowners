<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use DZunke\PanalyCodeOwners\Metric\OwnedDirectoriesCount;
use DZunke\PanalyCodeOwners\Metric\OwnedDirectoriesListing;
use DZunke\PanalyCodeOwners\Metric\OwnedFilesCount;
use DZunke\PanalyCodeOwners\Metric\OwnedFilesListing;
use DZunke\PanalyCodeOwners\Metric\UnownedDirectories;
use DZunke\PanalyCodeOwners\Parser\Parser;
use Panaly\Configuration\ConfigurationFile;
use Panaly\Configuration\RuntimeConfiguration;
use Panaly\Event\BeforeMetricCalculate;
use Panaly\Plugin\Plugin;

class CodeOwnersPlugin implements Plugin
{
    public function initialize(
        ConfigurationFile $configurationFile,
        RuntimeConfiguration $runtimeConfiguration,
        array $options,
    ): void {
        $parser = new Parser(
            $runtimeConfiguration->getWorkingDirectory(),
            $runtimeConfiguration->getLogger(),
        );

        $runtimeConfiguration->getEventDispatcher()->addListener(
            BeforeMetricCalculate::class,
            new WriteCodeOwnersToMetrics(
                $pluginOptions = PluginOptions::fromArray($options),
                $parser,
            ),
        );

        $this->registerPlugins($runtimeConfiguration, $parser, $pluginOptions);
    }

    public function registerPlugins(
        RuntimeConfiguration $configuration,
        Parser $parser,
        PluginOptions $pluginOptions,
    ): void {
        $configuration->addMetric(new OwnedDirectoriesCount($parser, $pluginOptions));
        $configuration->addMetric(new OwnedDirectoriesListing($parser, $pluginOptions));

        $configuration->addMetric(new OwnedFilesCount($parser, $pluginOptions));
        $configuration->addMetric(new OwnedFilesListing($parser, $pluginOptions));

        $configuration->addMetric(new UnownedDirectories($parser, $pluginOptions));
    }
}
