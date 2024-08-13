<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test;

use DZunke\PanalyCodeOwners\CodeOwnersPlugin;
use DZunke\PanalyCodeOwners\Metric\OwnedDirectoriesCount;
use DZunke\PanalyCodeOwners\Metric\OwnedDirectoriesListing;
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
        $runtimeConfiguration = $this->createMock(RuntimeConfiguration::class);

        $matcher = $this->exactly(5);
        $runtimeConfiguration->expects($matcher)
            ->method('addMetric')
            ->willReturnCallback(static function (object $metric) use ($matcher): void {
                match ($matcher->numberOfInvocations()) {
                    1 => self::assertInstanceOf(OwnedDirectoriesCount::class, $metric),
                    2 => self::assertInstanceOf(OwnedDirectoriesListing::class, $metric),
                    3 => self::assertInstanceOf(OwnedFilesCount::class, $metric),
                    4 => self::assertInstanceOf(OwnedFilesListing::class, $metric),
                    5 => self::assertInstanceOf(UnownedDirectories::class, $metric),
                    default => self::fail('Too much is going on here!'),
                };
            });

        (new CodeOwnersPlugin())->initialize(
            self::createStub(ConfigurationFile::class),
            $runtimeConfiguration,
            [
                'codeowners' => __DIR__ . '/Fixture/CODEOWNERS_Github',
                'replace' => [['metric' => 'foo', 'option' => 'bar', 'owners' => ['@foo']]],
            ],
        );
    }
}
