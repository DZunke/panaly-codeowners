<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test;

use CodeOwners\Parser;
use DZunke\PanalyCodeOwners\CodeOwnerParser;
use DZunke\PanalyCodeOwners\PluginOptions;
use DZunke\PanalyCodeOwners\WriteCodeOwnersToMetrics;
use Panaly\Configuration\ConfigurationFile\Metric;
use Panaly\Event\BeforeMetricCalculate;
use PHPUnit\Framework\TestCase;

class WriteCodeOwnersToMetricsTest extends TestCase
{
    public function testMetricOptionsAreReplacedWithCodeOwnerPaths(): void
    {
        $pluginOptions = PluginOptions::fromArray(
            [
                'codeowners' => __DIR__ . '/Fixture/CODEOWNERS',
                'replace' => [['metric' => 'foo', 'option' => 'bar', 'owners' => ['@Hulk', '@DrStrange', '@Unknown']]],
            ],
        );

        $metric = new Metric('foo', 'bar', 'baz', []);
        $event  = new BeforeMetricCalculate($metric, ['paths' => null]);

        (new WriteCodeOwnersToMetrics(
            $pluginOptions,
            new CodeOwnerParser(new Parser()),
        ))($event);

        self::assertSame(['src/PluginOptions', 'LICENSE'], $event->getOption('bar'));
    }
}
