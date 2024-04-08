<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test;

use DZunke\PanalyCodeOwners\CodeOwnersPlugin;
use DZunke\PanalyCodeOwners\WriteCodeOwnersToMetrics;
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
}
