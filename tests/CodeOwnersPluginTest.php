<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test;

use DZunke\PanalyCodeOwners\CodeOwnersPlugin;
use DZunke\PanalyCodeOwners\Metric\OwnedDirectoriesCount;
use DZunke\PanalyCodeOwners\Metric\OwnedFilesCount;
use DZunke\PanalyCodeOwners\Metric\OwnedFilesListing;
use DZunke\PanalyCodeOwners\Metric\UnownedDirectories;
use DZunke\PanalyCodeOwners\WriteCodeOwnersToMetrics;
use InvalidArgumentException;
use Panaly\Configuration\ConfigurationFile;
use Panaly\Configuration\RuntimeConfiguration;
use Panaly\Event\BeforeMetricCalculate;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CodeOwnersPluginTest extends TestCase
{
    public function testThatThePluginCanSuccessfullyBeInitializedAndListenerRegistered(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher->expects($this->once())->method('addListener')->with(
            BeforeMetricCalculate::class,
            self::isInstanceOf(WriteCodeOwnersToMetrics::class),
        );

        $runtimeConfiguration = self::createStub(RuntimeConfiguration::class);
        $runtimeConfiguration->method('getEventDispatcher')->willReturn($eventDispatcher);

        $plugin = new CodeOwnersPlugin();
        $plugin->initialize(
            new ConfigurationFile([], [], [], []),
            $runtimeConfiguration,
            [
                'codeowners' => __DIR__ . '/Fixture/CODEOWNERS_Github',
                'replace' => [['metric' => 'foo', 'option' => 'bar', 'owners' => ['@foo']]],
            ],
        );
    }

    public function testPluginInitializationWithInvalidConfiguration(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $runtimeConfiguration = self::createStub(RuntimeConfiguration::class);

        $plugin = new CodeOwnersPlugin();
        $plugin->initialize(
            new ConfigurationFile([], [], [], []),
            $runtimeConfiguration,
            ['invalid_option' => 'value'],
        );
    }

    public function testGetAvailableMetricsWhenInitialized(): void
    {
        $runtimeConfiguration = self::createStub(RuntimeConfiguration::class);
        $plugin               = new CodeOwnersPlugin();
        $plugin->initialize(
            new ConfigurationFile([], [], [], []),
            $runtimeConfiguration,
            [
                'codeowners' => __DIR__ . '/Fixture/CODEOWNERS_Github',
                'replace' => [['metric' => 'foo', 'option' => 'bar', 'owners' => ['@foo']]],
            ],
        );

        $metrics = $plugin->getAvailableMetrics([]);
        self::assertCount(5, $metrics);
        self::assertInstanceOf(OwnedDirectoriesCount::class, $metrics[0]);
        self::assertInstanceOf(UnownedDirectories::class, $metrics[1]);
        self::assertInstanceOf(OwnedFilesCount::class, $metrics[2]);
        self::assertInstanceOf(OwnedDirectoriesCount::class, $metrics[3]);
        self::assertInstanceOf(OwnedFilesListing::class, $metrics[4]);
    }

    public function testGetAvailableMetricsWhenNotInitialized(): void
    {
        $plugin  = new CodeOwnersPlugin();
        $metrics = $plugin->getAvailableMetrics([]);
        self::assertEmpty($metrics);
    }
}
