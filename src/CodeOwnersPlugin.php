<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use CodeOwners\Parser;
use Panaly\Configuration\ConfigurationFile;
use Panaly\Configuration\RuntimeConfiguration;
use Panaly\Event\BeforeMetricCalculate;
use Panaly\Plugin\BasePlugin;

class CodeOwnersPlugin extends BasePlugin
{
    public function initialize(
        ConfigurationFile $configurationFile,
        RuntimeConfiguration $runtimeConfiguration,
        array $options,
    ): void {
        $runtimeConfiguration->getEventDispatcher()->addListener(
            BeforeMetricCalculate::class,
            new WriteCodeOwnersToMetrics(
                PluginOptions::fromArray($options),
                new CodeOwnerParser(new Parser()),
            ),
        );
    }
}
